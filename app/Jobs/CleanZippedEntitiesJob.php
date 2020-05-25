<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;

class CleanZippedEntitiesJob implements ShouldQueue
{
    private $taskId;

    use Dispatchable;
    use InteractsWithQueue;
    use Queueable;
    use SerializesModels;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($taskId)
    {
        //
        $this->taskId = $taskId;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $disks = [
            'temporaryTasks',
            'tasks',
        ];

        foreach ($disks as $disk) {
            $path = \Storage::disk($disk)->path('');

            collect(\Storage::disk($disk)->files($this->taskId))
            ->each(
                function ($file) use ($path) {
                    fclose(fopen($path.$file, 'rb'));
                }
            );

            \Storage::disk($disk)->deleteDirectory($this->taskId);
        }
    }
}
