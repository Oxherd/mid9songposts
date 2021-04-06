<?php

namespace Tests\Feature\Listeners;

use App\Models\Link;
use App\Models\Post;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\Setup\Pages\WorksWithBahaPages;
use Tests\TestCase;

class ExtractContentLinksTest extends TestCase
{
    use RefreshDatabase;
    use WorksWithBahaPages;

    /** @test */
    public function it_will_extract_links_from_posts_content_and_save_into_database()
    {
        $this->assertCount(0, Link::all());

        Post::factory()->forPoster()->create();

        $this->assertCount(1, Link::all());
    }

    /** @test */
    public function it_will_delete_all_associated_links_when_extract_links()
    {
        $post = Post::factory()->forPoster()->create();

        $this->assertCount(1, Link::all());

        $post->extractLinks();

        $this->assertCount(1, Link::all());
    }

    /** @test */
    public function extracted_links_is_associated_with_post_and_poster_from_the_same_post()
    {
        $post = Post::factory()->forPoster()->create();

        $link = Link::first();

        $this->assertEquals($post->id, $link->post_id);
        $this->assertEquals($post->poster->id, $link->poster_id);
    }

    /** @test */
    public function it_will_toggle_post_has_music_column_if_there_is_no_music_get_extracted()
    {
        $noMusic = Post::factory()->forPoster()->create(['content' => 'foo bar']);

        $this->assertFalse($noMusic->has_music);
    }
}
