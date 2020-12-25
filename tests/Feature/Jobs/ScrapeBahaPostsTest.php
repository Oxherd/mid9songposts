<?php

namespace Tests\Feature\Jobs;

use App\Jobs\ScrapeBahaPosts;
use App\Models\Post;
use App\Models\Poster;
use App\Models\Thread;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\Client\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;
use PharIo\Manifest\InvalidUrlException;
use Tests\Setup\Pages\WorksWithBahaPages;
use Tests\TestCase;

class ScrapeBahaPostsTest extends TestCase
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

        ScrapeBahaPosts::dispatchSync($this->bahaUrl);

        $this->assertCount(1, Thread::all());
        $this->assertCount(20, Post::all());
        $this->assertCount(20, Poster::all());
    }

    /** @test */
    public function it_can_scrape_continuous_page_if_there_is_more()
    {
        $this->fakeThreadResponse();

        ScrapeBahaPosts::dispatchSync($this->bahaUrl, $switchPage = true);

        Http::assertSentCount(3);

        $this->assertCount(1, Thread::all());
        $this->assertCount(41, Post::all());
        $this->assertCount(41, Poster::all());

        Http::assertSent(function (Request $request) {
            return $request->url() === 'https://forum.gamer.com.tw/C.php?bsn=60076&snA=6004847';
        });

        Http::assertSent(function (Request $request) {
            return $request->url() === 'https://forum.gamer.com.tw/C.php?bsn=60076&snA=6004847&page=2';
        });

        Http::assertSent(function (Request $request) {
            return $request->url() === 'https://forum.gamer.com.tw/C.php?bsn=60076&snA=6004847&page=3';
        });
    }

    /** @test */
    public function it_also_work_as_single_post_page()
    {
        $this->fakeSinglePostResponse();

        ScrapeBahaPosts::dispatchSync($this->singlePostUrl);

        $this->assertDatabaseHas('threads', [
            'no' => '6055013',
            'title' => '【情報】12/8 半夜歌串一人一首',
            'date' => '2020-12-08',
        ]);

        $this->assertCount(1, Post::all());
        $this->assertCount(1, Poster::all());
    }

    /** @test */
    public function scraped_post_is_belongs_to_a_thread_that_created_from_same_page()
    {
        $this->fakePageOnePostsResponse();

        ScrapeBahaPosts::dispatchSync($this->bahaUrl);

        $this->assertEquals(Post::first()->thread_id, Thread::first()->id);
    }

    /** @test */
    public function a_post_is_belongs_to_a_poster_that_created_from_same_page()
    {
        $this->fakePageOnePostsResponse();

        ScrapeBahaPosts::dispatchSync($this->bahaUrl);

        $this->assertEquals(Post::first()->poster_id, Poster::first()->id);
    }
}
