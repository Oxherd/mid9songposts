<?php

namespace Tests\Feature\Jobs;

use App\Jobs\CleanUpPostsWithoutLink;
use App\Models\Post;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Event;
use Tests\TestCase;

class CleanUpPostsWithoutLinkTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function it_will_find_out_posts_has_no_link_and_tag_them_has_no_music()
    {
        Event::fake();

        $post = Post::factory()->create(['content' => 'foo bar baz']);

        $this->assertTrue($post->has_music);

        CleanUpPostsWithoutLink::dispatchNow();

        $post->refresh();

        $this->assertFalse($post->has_music);
    }
}
