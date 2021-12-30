<?php

namespace Tests\Feature\Jobs;

use App\Jobs\ScrapeBahaPostsContinuously;
use App\Jobs\ScrapePostsFromSearchTitle;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Queue;
use Tests\Setup\Pages\WorksWithBahaPages;
use Tests\TestCase;

class ScrapePostsFromSearchTitleTest extends TestCase
{
    use WorksWithBahaPages;

    protected function setUp(): void
    {
        parent::setUp();

        Queue::fake();
    }

    /** @test */
    public function it_will_resolve_and_dispatch_scrape_posts_tasks()
    {
        $this->fakeOnePageSearchTitleResponse();

        (new ScrapePostsFromSearchTitle(
            $title = 'foobar',
            $page = 1,
            $user = null
        ))->handle();

        Http::assertSent(function ($request) {
            return $request->url() == 'https://forum.gamer.com.tw/B.php?bsn=60076&qt=1&q=foobar&page=1';
        });

        Queue::assertPushed(ScrapeBahaPostsContinuously::class, 30);
    }
}
