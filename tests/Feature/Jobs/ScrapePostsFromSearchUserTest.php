<?php

namespace Tests\Feature\Jobs;

use App\Jobs\ScrapeBahaPosts;
use App\Jobs\ScrapePostsFromSearchUser;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Queue;
use Tests\Setup\Pages\WorksWithBahaPages;
use Tests\TestCase;

class ScrapePostsFromSearchUserTest extends TestCase
{
    use WorksWithBahaPages;

    protected function setUp(): void
    {
        parent::setUp();

        Queue::fake();
    }

    /** @test */
    public function it_get_all_related_posts_and_dispatch_job_in_order_to_save_them_into_database()
    {
        $this->fakeOnePageSearchUserResponse();

        ScrapePostsFromSearchUser::dispatchNow('foobar666');

        Queue::assertPushed(ScrapeBahaPosts::class, 29);
    }

    /** @test */
    public function it_can_continue_get_more_posts_and_dispatch_more_jobs_in_next_page()
    {
        $this->fakeAllPageSearchUserResponse();

        ScrapePostsFromSearchUser::dispatchNow('foobar666', $switchPage = true);

        Queue::assertPushed(ScrapeBahaPosts::class, 29 + 30);

        Http::assertSentCount(2);

        Http::assertSent(function ($request) {
            return $request->url() === 'https://forum.gamer.com.tw/Bo.php?bsn=60076&qt=6&q=foobar666';
        });

        Http::assertSent(function ($request) {
            return $request->url() === 'https://forum.gamer.com.tw/Bo.php?bsn=60076&qt=6&q=foobar666&page=2';
        });
    }
}
