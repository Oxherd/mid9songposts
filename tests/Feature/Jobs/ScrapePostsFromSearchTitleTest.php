<?php

namespace Tests\Feature\Jobs;

use App\Jobs\ScrapeBahaPostsContinuously;
use App\Jobs\ScrapePostsFromSearchTitle;
use Illuminate\Support\Facades\Queue;
use Tests\TestCase;

class ScrapePostsFromSearchTitleTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        Queue::fake();
    }

    /** @test */
    public function it_will_resolve_and_dispatch_scrape_posts_tasks()
    {
        $this->mockClientWithSearchTitleFirstPage();

        (new ScrapePostsFromSearchTitle(
            $title = 'foobar',
            $page = 1,
            $user = null
        ))->handle();

        Queue::assertPushed(ScrapeBahaPostsContinuously::class, 30);
    }
}
