<?php

namespace App\Jobs;

use App\TwUtils\Base\Job;
use App\Models\SocialUser;
use App\TwUtils\TwitterOperations\FetchUserInfoOperation;

class FetchUserInfoJob extends Job
{
    private $socialUser;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct(SocialUser $socialUser)
    {
        $this->socialUser = $socialUser;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $fetchUserInfoOperation = new FetchUserInfoOperation();

        $fetchUserInfoOperation->setSocialUser($this->socialUser)->dispatch();
    }
}
