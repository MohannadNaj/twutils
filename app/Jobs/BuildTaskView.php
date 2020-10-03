<?php

namespace App\Jobs;

use App\Task;
use App\Media;
use App\Tweet;
use App\TaskView;
use Illuminate\Support\Arr;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;

class BuildTaskView implements ShouldQueue
{
    use Dispatchable;
    use InteractsWithQueue;
    use Queueable;
    use SerializesModels;

    protected Task $task;

    public function __construct(Task $task)
    {
        $this->task = $task->fresh();
    }

    public function handle()
    {
        $taskView = new TaskView ([
            'task_id' => $this->task->id,
        ]);

        if (! in_array($this->task->type, Task::TWEETS_LISTS_TYPES) )
        {
            return ;
        }

        $months = [];

        $this->task->tweets()->chunk(100, function ($tweets) use (& $taskView, & $months) {
            foreach ($tweets as $tweet) {
                $taskView->count += 1;

                $monthPath = $tweet->tweet_created_at->year . '.' . $tweet->tweet_created_at->month;

                Arr::set(
                    $months,
                    $monthPath,
                    Arr::get($months, $monthPath)+1
                );

                if ($tweet->media->isEmpty())
                {
                    $taskView->tweets_text_only += 1;
                    continue;
                }

                $taskView->tweets_with_photos += $tweet->media
                    ->filter(
                        fn (Media $media) => $media->type === Media::TYPE_PHOTO)
                    ->count();

                $taskView->tweets_with_videos += $tweet->media
                    ->filter(
                        fn (Media $media) => $media->type === Media::TYPE_VIDEO)
                    ->count();

                $taskView->tweets_with_gifs += $tweet->media
                    ->filter(
                        fn (Media $media) => $media->type === Media::TYPE_ANIMATED_GIF)
                    ->count();
            }
        });

        $taskView->months = $months;

        $taskView->save();
    }
}
