<?php

namespace App\Jobs;

use App\Helpers\Pause;
use App\Posts\Baha\PostSection;
use App\Posts\Baha\ThreadPage;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use InvalidArgumentException;

class ScrapeBahaPosts implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * target scrape url
     *
     * @property string
     */
    public $url;

    /**
     * determin scrape process will continue if there if more page after
     *
     * @property bool
     */
    public $switchPage;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($url, $switchPage = false)
    {
        $this->url = $url;
        $this->switchPage = $switchPage;
    }

    /**
     * It will scrape baha's posts and related data from given url
     *
     * catchs \InvalidArgumentException when save up thread
     * the page might be unavailable and can't get related data
     * therefore do nothing
     *
     * @return void
     */
    public function handle()
    {
        $threadPage = new ThreadPage($this->url);

        try {
            $thread = $threadPage->save();
        } catch (InvalidArgumentException $e) {
            return;
        }

        do {
            $threadPage->posts()->each(function (PostSection $section) use ($thread) {
                $section->save($thread->id);
            });

            if (!$this->switchPage) {
                break;
            }

            Pause::seconds();
        } while ($threadPage = $threadPage->nextPage());
    }
}
