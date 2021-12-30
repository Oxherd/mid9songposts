<?php

namespace App\Jobs;

use App\Posts\Baha\SearchTitle;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class ScrapePostsFromSearchTitleContinuously implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * search target title
     *
     * @property string
     */
    public $title;

    /**
     * which page should scrape from
     *
     * @property bool
     */
    public $page;

    /**
     * get specific result from this user
     *
     * @property string|null
     */
    public $user;

    /**
     * Create a new job instance.
     *
     * @param string $title it will check string is urlencoded or not
     *
     * @param bool $page
     *
     * @return void
     */
    public function __construct($title = '半夜歌串一人一首', $page = 1, $user = 'a7752876')
    {
        $this->title = urldecode($title) === $title ? urlencode($title) : $title;
        $this->page = $page;
        $this->user = $user;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $searchTitle = new SearchTitle(
            "https://forum.gamer.com.tw/B.php?bsn=60076&qt=1&q={$this->title}&page={$this->page}"
        );

        $searchTitle->handle($this->user);

        if ($searchTitle->hasNextPage()) {
            self::dispatch($this->title, $this->page + 1, $this->user);
        }
    }
}
