<?php

namespace Tests\Feature\Jobs;

use App\Jobs\ScrapeBahaPostsContinuously;
use App\Jobs\ScrapePostsFromSearchTitleContinuously;
use Illuminate\Support\Facades\Queue;
use Tests\TestCase;

class ScrapePostsFromSearchTitleContinuouslyTest extends TestCase
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

        $job = new ScrapePostsFromSearchTitleContinuously('foobar', 1, null);

        $job->handle();

        Queue::assertPushed(ScrapeBahaPostsContinuously::class, 30);
    }

    /** @test */
    public function it_will_dispatch_a_new_job_to_continue_same_process_with_next_page()
    {
        $this->mockClientWithSearchTitleFirstPage();

        (new ScrapePostsFromSearchTitleContinuously(
            $title = 'foobar',
            $page = 1,
            $user = 'foobar666'
        )
        )->handle();

        Queue::assertPushed(
            function (ScrapePostsFromSearchTitleContinuously $job)
            use ($title, $page, $user) {
                return $job->title == $title &&
                    $job->page == 2 &&
                    $job->user == $user;
            }
        );
    }

    /** @test */
    public function it_wont_dispatch_a_new_job_if_there_is_no_more_page_to_scrape()
    {
        $this->mockClientWithSearchTitleLastPage();

        $job = new ScrapePostsFromSearchTitleContinuously();

        $job->handle();

        Queue::assertPushed(ScrapePostsFromSearchTitleContinuously::class, 0);
    }
}
