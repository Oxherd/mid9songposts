<?php

namespace Tests\Feature\PostCreated;

use App\Jobs\FetchPostComments;
use App\Models\Comment;
use App\Models\Post;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\Client\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Queue;
use Tests\Setup\Pages\WorksWithBahaPages;
use Tests\TestCase;

class FetchPostCommentsTest extends TestCase
{
    use RefreshDatabase;
    use WorksWithBahaPages;

    /** @test */
    public function it_push_a_new_job_when_a_post_was_created()
    {
        Queue::fake();

        Post::factory()->create();

        Queue::assertPushed(FetchPostComments::class);
    }

    /** @test */
    public function it_send_a_http_request_in_order_to_get_all_comments_from_a_post()
    {
        Http::fake();

        $this->fakeFetchSingleCommentResponse();

        $post = Post::factory()->create();

        FetchPostComments::dispatchSync($post);

        Http::assertSent(function (Request $request) use ($post) {
            return $request->url() === "https://forum.gamer.com.tw/ajax/moreCommend.php?bsn=60076&snB={$post->no}";
        });
    }

    /** @test */
    public function it_saves_up_fetched_comments()
    {
        $this->fakeFetchSingleCommentResponse();

        $post = Post::factory()->create();

        $this->assertDatabaseCount('comments', 0);

        FetchPostComments::dispatchSync($post);

        $this->assertDatabaseHas('comments', [
            'content' => 'This is my comment.',
            'created_at' => '2021-07-22 17:35:55',
            'inserted_at' => now()->toDateTimeString(),
        ]);
    }

    /** @test */
    public function it_can_saves_multiple_comments()
    {
        $this->fakeFetchMultipleCommentsResponse();

        $post = Post::factory()->create();

        $this->assertDatabaseCount('comments', 0);

        FetchPostComments::dispatchSync($post);

        $this->assertDatabaseCount('comments', 2);

        $this->assertEquals(Comment::find(1)->created_at, '2021-07-24 23:27:22');
        $this->assertEquals(Comment::find(2)->created_at, '2021-07-23 19:38:14');
    }

    /** @test */
    public function saved_comment_blongs_to_a_post()
    {
        $this->fakeFetchSingleCommentResponse();

        $post = Post::factory()->create();

        FetchPostComments::dispatchSync($post);

        $comment = Comment::first();

        $this->assertEquals($post->id, $comment->post_id);
    }

    /** @test */
    public function saved_comment_belongs_to_a_poster__if_poster_not_exists_then_create_it()
    {
        $this->fakeFetchSingleCommentResponse();

        $post = Post::factory()->create();

        FetchPostComments::dispatchSync($post);

        $comment = Comment::first();

        $this->assertDatabaseHas('posters', [
            'id' => $comment->poster_id,
        ]);
    }
}
