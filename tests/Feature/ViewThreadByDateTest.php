<?php

namespace Tests\Feature;

use App\Models\Comment;
use App\Models\Link;
use App\Models\Post;
use App\Models\Poster;
use App\Models\Thread;
use HTMLPurifier;
use Tests\TestCase;

class ViewThreadByDateTest extends TestCase
{
    /** @test */
    public function it_shows_specified_thread_by_provides_corresponding_date()
    {
        Thread::factory()->create([
            'date' => $date = today()->format('Y-m-d'),
            'title' => '今天天氣晴',
        ]);

        $response = $this->get(route('threads.show', [
            'thread' => $date,
        ]));

        $response->assertSee([$date, '今天天氣晴']);
    }

    /** @test */
    public function it_lists_all_posts_related_to_that_thread()
    {
        $thread = Thread::factory()->create();

        Post::factory()->for($thread)->create(['content' => 'This is my post']);
        Post::factory()->create(['content' => 'This is a post from other thread']);

        $response = $this->get(route('threads.show', [
            'thread' => $thread->date,
        ]));

        $response->assertSee('This is my post');
        $response->assertDontSee('This is a post from other thread');
    }

    /** @test */
    public function it_lists_all_posters_from_posts_related_to_that_thread()
    {
        $thread = Thread::factory()->create();

        $poster = Poster::factory()->create(['account' => 'johndoe']);

        Post::factory()->for($poster)->for($thread)->create();

        $response = $this->get(route('threads.show', [
            'thread' => $thread->date,
        ]));

        $response->assertSee('johndoe');
    }

    /** @test */
    public function it_lists_all_links_from_posts_related_to_that_thread()
    {
        $thread = Thread::factory()->create();

        $link = Link::factory()
            ->for(Post::factory()->for($thread))
            ->create();

        $response = $this->get(route('threads.show', [
            'thread' => $thread->date,
        ]));

        $response->assertSee($link->embedded());
    }

    /** @test */
    public function it_lists_all_comments_from_posts_related_to_that_thread()
    {
        $thread = Thread::factory()->create();

        $comment = Comment::factory()
            ->for(Post::factory()->for($thread))
            ->create();

        $response = $this->get(route('threads.show', [
            'thread' => $thread->date,
        ]));

        $response->assertSee($comment->content);
        $response->assertSee($comment->poster->account);
    }
}
