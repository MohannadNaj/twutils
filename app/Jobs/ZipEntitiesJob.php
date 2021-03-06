<?php

namespace App\Jobs;

use App\Models\Export;
use App\Models\MediaFile;
use App\TwUtils\Base\Job;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use App\TwUtils\Services\ExportsService;

class ZipEntitiesJob extends Job
{
    protected $export;

    public $deleteWhenMissingModels = true;

    protected ExportsService $exportsService;

    public function __construct(Export $export)
    {
        $this->queue = 'exports';
        $this->export = $export;
        $this->exportsService = app(ExportsService::class);
    }

    public function handle()
    {
        try {
            $this->start();
        } catch (\Exception $e) {
            \Log::warning($e);

            $this->export->status = Export::STATUS_BROKEN;
            $this->export->save();
        }
    }

    protected function start()
    {
        $paths = collect();
        $this->export
            ->task
            ->likes
            ->load('media.mediaFiles')
            ->pluck('media.*.mediaFiles.*')
            ->map(function ($mediaFilesCollection) use ($paths) {
                return collect($mediaFilesCollection)->map(function ($mediaFile) use ($paths) {
                    if ($mediaFile->status === MediaFile::STATUS_SUCCESS) {
                        $paths->push($mediaFile->mediaPath);
                    }
                });
            });

        Storage::disk('local')->makeDirectory($this->export->id);

        $paths->map(function ($path) {
            if (Storage::disk('local')->exists($this->export->id.'/'.$path)) {
                return;
            }

            if (MediaFile::getCacheStorageDisk()->exists($path)) {
                Storage::disk('local')->put($this->export->id.'/'.$path, MediaFile::getCacheStorageDisk()->readStream($path));

                return;
            }

            try {
                Storage::disk('local')->put($this->export->id.'/'.$path, MediaFile::getStorageDisk()->readStream($path));
            } catch (\Exception $e) {
                Log::warning($e);
            }
        });

        $fileName = $this->export->id.'.zip';

        $fileAbsolutePath = Storage::disk('local')->path($this->export->id).'/'.$fileName;

        $zipFile = $this->exportsService->makeTaskZipObject($this->export);

        // Include media in the zip file, and save it
        foreach (collect(Storage::disk('local')->allFiles($this->export->id))
        ->chunk(5) as $filesChunk) {
            $filesChunk->map(function ($file) use (&$zipFile) {
                $zipFile->addFile(Storage::disk('local')->path($file), 'media/'.Str::after($file, '/'));
            });
        }

        $zipFile
        ->saveAsFile($fileAbsolutePath)
        ->close();

        $zippedStream = fopen($fileAbsolutePath, 'r');

        Storage::disk(config('filesystems.cloud'))->put($this->export->id, $zippedStream);

        fclose($zippedStream);

        Storage::disk('local')->deleteDirectory($this->export->id);

        $this->export->status = 'success';
        $this->export->progress = $this->export->fresh()->progress_end;

        // Check export wasn't removed while processing..
        if ($this->export->fresh()) {
            $this->export->save();
        }
    }
}
