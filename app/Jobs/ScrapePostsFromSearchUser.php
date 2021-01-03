<?php

namespace App\Jobs;

use App\Helpers\Pause;
use App\Posts\Baha\SearchUser;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Str;
use InvalidArgumentException;

class ScrapePostsFromSearchUser implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * @property string
     */
    public $user;

    /**
     * determine scrape next page or not
     *
     * @property bool
     */
    protected $switchPage;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($user, $switchPage = false)
    {
        $this->user = $user;
        $this->switchPage = $switchPage;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $searchResult = new SearchUser("https://forum.gamer.com.tw/Bo.php?bsn=60076&qt=6&q={$this->user}");

        do {
            $searchResult
                ->filter('半夜歌串一人一首')
                ->getLinks()
                ->each(function ($link) {
                    ScrapeBahaPosts::dispatch($link);
                });

            if (!$this->switchPage) {
                break;
            }

            Pause::seconds();
        } while ($searchResult = $searchResult->nextPage());
    }
}
