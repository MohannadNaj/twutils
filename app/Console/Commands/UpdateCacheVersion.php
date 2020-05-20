<?php

namespace App\Console\Commands;

use Http;
use Cache;
use Illuminate\Console\Command;

class UpdateCacheVersion extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'update-cache-version';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Update cache version';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        try {
            $this->updateCacheVersion();
        } catch (\Exception $e) {
            if (app('env') !== 'production')
            {
                throw $e;
            }
        }
    }

    protected function updateCacheVersion()
    {
        $response = Http::withHeaders([
            'Authorization' => 'token ' . env('GITHUB_TOKEN'),
        ])->get('https://api.github.com/repos/MohannadNaj/twutils/tags');

        $jsonResponse = $response->json();

        if (empty($versionFound = $jsonResponse[0]['name'] ?? null))
        {
            throw new \Exception("Couldn't find version. GitHub Response: \n" . $response->body(), 1);
        }

        Cache::set('app.version', $versionFound);
    }
}
