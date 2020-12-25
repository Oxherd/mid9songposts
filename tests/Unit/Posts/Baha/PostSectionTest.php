<?php

namespace Tests\Unit\Posts\Baha;

use App\Models\Post;
use App\Models\Poster;
use App\Models\Thread;
use App\Posts\Baha\PosterData;
use App\Posts\Baha\PostSection;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Str;
use Tests\Setup\Pages\WorksWithBahaPages;
use Tests\TestCase;

class PostSectionTest extends TestCase
{
    use WorksWithBahaPages;
    use RefreshDatabase;

    /** @test */
    public function it_can_save_post_includes_poster_data_into_database()
    {
        $postSection = new PostSection($this->postSectionHtml());

        $this->assertCount(0, Post::all());
        $this->assertCount(0, Poster::all());

        $postSection->save();

        $this->assertCount(1, Post::all());
        $this->assertCount(1, Poster::all());

        $this->assertDatabaseHas('posts', [
            'no' => '69887489',
            'created_at' => '2020-11-07 23:10:35',
            'inserted_at' => now()->toDateTimeString(),
        ]);
    }

    /** @test */
    public function saved_post_is_related_to_a_poster_from_same_section()
    {
        (new PostSection($this->postSectionHtml()))->save();

        $post = Post::first();
        $poster = Poster::first();

        $this->assertEquals($post->poster_id, $poster->id);
    }

    /** @test */
    public function it_wont_create_same_two_row_of_posts_into_database()
    {
        $postSection = new PostSection($this->postSectionHtml());

        $postSection->save();

        $this->assertCount(1, Post::all());

        $postSection->save();

        $this->assertCount(1, Post::all());
    }

    /** @test */
    public function passing_thread_id_param_to_create_relationship_with_target_thread()
    {
        $postSection = new PostSection($this->postSectionHtml());

        $thread = Thread::create([
            'no' => 'thread_no',
            'title' => '半夜歌串...',
            'date' => now()->toDateString(),
        ]);

        $post = $postSection->save($thread->id);

        $this->assertEquals($thread->id, $post->thread_id);
    }

    /** @test */
    public function it_can_get_post_index_from_html_section()
    {
        $postSection = new PostSection($this->postSectionHtml());

        $this->assertEquals('69887489', $postSection->index());
    }

    /** @test */
    public function it_can_get_post_content_from_html_section()
    {
        $postSection = new PostSection($this->postSectionHtml());

        $this->assertTrue(Str::contains($postSection->content(), '今天根本就夏天吧'));
    }

    /** @test */
    public function it_can_get_the_post_created_time_from_html_section()
    {
        $postSection = new PostSection($this->postSectionHtml());

        $this->assertEquals('2020-11-07 23:10:35', $postSection->createdAt());
    }

    /** @test */
    public function it_will_pass_poster_data_into_its_own_instance()
    {
        $postSection = new PostSection($this->postSectionHtml());

        $this->assertInstanceOf(PosterData::class, $postSection->poster());
    }
}
