<?php

namespace App\Jobs;

use App\Helpers\Pause;
use App\Posts\Baha\SearchTitle;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class ScrapePostsFromSearchTitle implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * search target title
     *
     * @property string
     */
    public $title;

    /**
     * determine continue scrape posts if there are more page
     *
     * @property bool
     */
    protected $switchPage;

    /**
     * get specific result from this user
     *
     * @property string|null
     */
    protected $user;

    /**
     * Create a new job instance.
     *
     * @param string $title it will check string is urlencoded or not
     *
     * @param bool $switchPage
     *
     * @return void
     */
    public function __construct($title, $switchPage = false, $user = 'a7752876')
    {
        $this->title = urldecode($title) === $title ? urlencode($title) : $title;
        $this->switchPage = $switchPage;
        $this->user = $user;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $searchTitle = new SearchTitle("https://forum.gamer.com.tw/B.php?bsn=60076&qt=1&q={$this->title}");

        do {
            $searchTitle
                ->filterByUser($this->user)
                ->getLinks()
                ->each(function ($link) {
                    ScrapeBahaPosts::dispatch($link, $nextPage = true);
                });

            if (!$this->switchPage) {
                break;
            }

            Pause::seconds();
        } while ($searchTitle = $searchTitle->nextPage());
    }
}
