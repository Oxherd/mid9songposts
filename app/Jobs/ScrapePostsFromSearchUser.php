<?php

namespace App\Jobs;

use App\Baha\SearchUser;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class ScrapePostsFromSearchUser implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * @property string
     */
    public $user;

    /**
     * determine scrape which page
     *
     * @property bool
     */
    protected $page;

    /**
     * only scrape post contains this title
     */
    protected $title;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($user, $page = 1, $title = '半夜歌串一人一首')
    {
        $this->user = $user;
        $this->page = $page;
        $this->title = $title;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $searchUser = new SearchUser(
            "https://forum.gamer.com.tw/Bo.php?bsn=60076&qt=6&q={$this->user}&page={$this->page}"
        );

        $searchUser->handle($this->title);
    }
}
