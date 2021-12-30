<?php

namespace Tests\Feature\Jobs;

use App\Jobs\ScrapeBahaPostsContinuously;
use App\Models\Post;
use App\Models\Poster;
use App\Models\Thread;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Queue;
use Tests\Setup\Pages\WorksWithBahaPages;
use Tests\TestCase;

class ScrapeBahaPostsContinuouslyTest extends TestCase
{
    use RefreshDatabase;
    use WorksWithBahaPages;

    /** @test */
    public function it_will_save_the_thread_and_all_posts_and_all_posters_from_scraped_html()
    {
        $this->fakePageOnePostsResponse();

        $this->assertCount(0, Thread::all());
        $this->assertCount(0, Post::all());
        $this->assertCount(0, Poster::all());

        ScrapeBahaPostsContinuously::dispatchSync($this->bahaUrl);

        $this->assertCount(1, Thread::all());
        $this->assertCount(20, Post::all());
        $this->assertCount(20, Poster::all());
    }

    /** @test */
    public function it_catchs_NotExpectedPageException_and_do_nothing_since_the_thread_or_post_probably_unavailable()
    {
        $this->withoutExceptionHandling();

        $this->fakeThreadUnavailableResponse();

        ScrapeBahaPostsContinuously::dispatchSync($this->bahaUrl);

        $this->assertCount(0, Thread::all());
    }

    /** @test */
    public function it_will_dispatch_a_new_job_to_process_the_next_thread_page_if_there_is_more()
    {
        Queue::fake();

        $this->fakePageOnePostsResponse();

        (new ScrapeBahaPostsContinuously($this->bahaUrl))->handle();

        Queue::assertPushed(function (ScrapeBahaPostsContinuously $job) {
            return $job->url == "https://forum.gamer.com.tw/C.php?bsn=60076&snA=6004847&page=2";
        });
    }
}
