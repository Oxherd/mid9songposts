<?php

namespace Tests\Feature\Jobs;

use App\Jobs\ScrapeBahaPostsContinuously;
use App\Jobs\ScrapePostsFromSearchTitleContinuously;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Queue;
use Tests\Setup\Pages\WorksWithBahaPages;
use Tests\TestCase;

class ScrapePostsFromSearchTitleContinuouslyTest extends TestCase
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

        $job = new ScrapePostsFromSearchTitleContinuously('foobar', 1, null);

        $job->handle();

        Http::assertSent(function ($request) {
            return $request->url() == 'https://forum.gamer.com.tw/B.php?bsn=60076&qt=1&q=foobar&page=1';
        });

        Queue::assertPushed(ScrapeBahaPostsContinuously::class, 30);
    }

    /** @test */
    public function it_will_dispatch_a_new_job_to_continue_same_process_with_next_page()
    {
        $this->fakeOnePageSearchTitleResponse();

        (new ScrapePostsFromSearchTitleContinuously(
            $title = 'foobar',
            $page = 1,
            $user = 'foobar666')
        )->handle();

        Queue::assertPushed(
            function (ScrapePostsFromSearchTitleContinuously $job)
             use ($title, $page, $user) {
                return $job->title == $title &&
                $job->page == 2 &&
                $job->user == $user;
            });
    }

    /** @test */
    public function it_wont_dispatch_a_new_job_if_there_is_no_more_page_to_scrape()
    {
        $this->fakeLastSearchTitlePageResponse();

        $job = new ScrapePostsFromSearchTitleContinuously();

        $job->handle();

        Queue::assertPushed(ScrapePostsFromSearchTitleContinuously::class, 0);
    }
}
