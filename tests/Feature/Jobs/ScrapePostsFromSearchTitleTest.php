<?php

namespace Tests\Feature\Jobs;

use App\Jobs\ScrapeBahaPosts;
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
    public function it_get_all_related_thread_and_dispatch_each_thread_a_job_to_save_them_into_database()
    {
        $this->fakeOnePageSearchTitleResponse();

        ScrapePostsFromSearchTitle::dispatchNow(
            $title = '半夜歌串一人一首',
            $nextPage = false,
            $user = null
        );

        Queue::assertPushed(ScrapeBahaPosts::class, 30);
    }

    /** @test */
    public function it_can_specify_searchable_thread_must_post_by_someone()
    {
        $this->fakeOnePageSearchTitleResponse();

        ScrapePostsFromSearchTitle::dispatchNow(
            $title = '半夜歌串一人一首',
            $nextPage = false,
            $user = 'foobar666'
        );

        Queue::assertPushed(ScrapeBahaPosts::class, 1);
    }

    /** @test */
    public function it_can_continue_get_rest_realted_thread_and_dispatch_more_jobs_if_there_is_more_page()
    {
        $this->fakeAllPageSearchTitleResponse();

        ScrapePostsFromSearchTitle::dispatchNow('songs', true, null);

        Queue::assertPushed(ScrapeBahaPosts::class, 60);

        Http::assertSentCount(2);

        Http::assertSent(function ($request) {
            return $request->url() === 'https://forum.gamer.com.tw/B.php?bsn=60076&qt=1&q=songs';
        });

        Http::assertSent(function ($request) {
            return $request->url() === 'https://forum.gamer.com.tw/B.php?bsn=60076&qt=1&q=songs&page=2';
        });
    }
}
