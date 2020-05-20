<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class DeleteMeJob implements ShouldQueue
{
    private $user;

    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function __construct($user)
    {
        $this->user = $user;
    }

    public function handle()
    {
        $user = $this->user->fresh();

        if (is_null($user)) {
            return;
        }

        $removeDate = $user->remove_at;

        if ($removeDate !== null && now()->greaterThanOrEqualTo($removeDate)) {
            $user->delete();
        }
    }
}
