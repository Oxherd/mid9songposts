<?php

namespace Tests\Feature\Jobs;

use App\Jobs\CleanUpPostsWithoutLink;
use App\Models\NoMusic;
use App\Models\Post;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Event;
use Tests\TestCase;

class CleanUpPostsWithoutLinkTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function it_can_get_posts_without_link_and_add_a_new_record_to_tag_them_with_no_music()
    {
        $post = Post::factory()->create(['content' => 'foo bar baz']);

        $this->assertTrue($post->has_music);

        CleanUpPostsWithoutLink::dispatchNow();

        $post->refresh();

        $this->assertFalse($post->has_music);
    }

    /** @test */
    public function it_extract_links_first_in_case_there_are_actual_url_in_content()
    {
        Event::fake();

        $post = Post::factory()->create();

        $this->assertCount(0, $post->links);

        CleanUpPostsWithoutLink::dispatchNow();

        $post->refresh();

        $this->assertCount(1, $post->links);
    }
}
