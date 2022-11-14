<?php

namespace Tests\Feature\Jobs;

use App\Jobs\ScrapeBahaPosts;
use App\Jobs\ScrapePostsFromSearchUser;
use Illuminate\Support\Facades\Queue;
use Tests\TestCase;

class ScrapePostsFromSearchUserTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        Queue::fake();
    }

    /** @test */
    public function it_will_resolve_and_dispatch_scrape_posts_tasks()
    {
        $this->mockClientWithSearchUserFirstPage();

        (new ScrapePostsFromSearchUser(
            $user = 'foobar666',
            $page = 1,
            $title = '半夜歌串一人一首'
        )
        )->handle();

        Queue::assertPushed(ScrapeBahaPosts::class, 29);

        Queue::assertPushed(function (ScrapeBahaPosts $job) {
            return $job->url == 'https://forum.gamer.com.tw/Co.php?bsn=60076&sn=80190131';
        });
    }
}
