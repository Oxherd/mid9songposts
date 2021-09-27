<?php

namespace Tests\Feature\Jobs;

use App\Jobs\FetchCommentsFromExistedPosts;
use App\Models\Comment;
use App\Models\Post;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Tests\Setup\Pages\WorksWithBahaPages;
use Tests\TestCase;

class FetchCommentsFromExistedPostsTest extends TestCase
{
    use RefreshDatabase;
    use WorksWithBahaPages;

    /**
     * the reason why not using Queue::fake is
     * Queue::bulk in FetchPostComments::class won't trigger pushed events
     */
    protected function setUp(): void
    {
        parent::setUp();

        Post::unsetEventDispatcher();
    }

    /** @test */
    public function it_will_query_all_posts_and_immediately_dispatch_FetchPostComment()
    {
        $this->fakeFetchSingleCommentResponse();

        Post::factory()->count(3)->create();

        $this->assertDatabaseCount('jobs', 0);

        FetchCommentsFromExistedPosts::dispatchSync();

        $this->assertDatabaseCount('jobs', 3);

        $this->assertEquals(3, DB::table('jobs')->where('queue', 'scrape')->count());
    }

    /** @test */
    public function it_wont_queue_post_who_already_has_comments()
    {
        $post = Post::factory()->create();

        Comment::create([
            'post_id' => $post->id,
            'poster_id' => $post->poster_id,
            'content' => 'foobar',
            'inserted_at' => now()->toDateTimeString(),
        ]);

        FetchCommentsFromExistedPosts::dispatchSync();

        $this->assertDatabaseCount('jobs', 0);
    }
}
