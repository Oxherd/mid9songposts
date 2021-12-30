<?php

namespace App\Jobs;

use App\Exceptions\NotExpectedPageException;
use App\Posts\Baha\ThreadPage;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class ScrapeBahaPostsContinuously implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * target scrape url
     *
     * @property string
     */
    public $url;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($url)
    {
        $this->url = $url;
    }

    /**
     * It will scrape baha's posts and related data from given url
     *
     * catchs NotExpectedPageException when save up thread
     * the page might be unavailable and can't get related data
     * therefore do nothing
     *
     * @return void
     */
    public function handle()
    {
        $threadPage = new ThreadPage($this->url);

        try {
            $threadPage->handle();
        } catch (NotExpectedPageException $e) {
            return;
        }

        if ($threadPage->hasNextPage()) {
            self::dispatch($threadPage->url()->nextPage());
        }
    }
}
