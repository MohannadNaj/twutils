<?php

namespace App\TwUtils\TwitterOperations;

use App\Jobs\DestroyTweetJob;

class destroyTweetsOperation extends destroyLikesOperation
{
    protected $shortName = 'DestroyTweets';
    protected $endpoint = 'statuses/destroy';
    protected $scope = 'write';
    protected $httpMethod = 'post';
    protected $dispatchJobName = DestroyTweetJob::class;
}
