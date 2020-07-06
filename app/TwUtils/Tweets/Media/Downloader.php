<?php

namespace App\TwUtils\Tweets\Media;

use Storage;
use App\TwUtils\State\DownloadStatus;

abstract class Downloader
{
    protected $media;
    protected $path;
    static $counter = 0;

    final public function __construct($media, $path)
    {
        $this->media = $media;
        $this->path = $path . ++ static::$counter;
    }

    abstract protected function getUrl() : string;

    final public function download() : DownloadStatus
    {
        $ok = false;
        $client = app('HttpClient');

        $response = $client->get($this->getUrl());

        $extension = app('MimeDB')->findExtension($response->getHeaderLine('Content-Type'));
        $localPath = $this->path.'.'.$extension;

        try {
            if (Storage::disk('temporaryTasks')->put($localPath, $response->getBody()->getContents())) {
                $ok = true;
            }
        } catch (\Exception $e) {
        }

        return new DownloadStatus($ok, $localPath);
    }
}