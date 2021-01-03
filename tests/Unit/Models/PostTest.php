<?php

namespace Tests\Unit\Models;

use App\Models\Link;
use App\Models\Post;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Event;
use Tests\Setup\Pages\WorksWithBahaPages;
use Tests\TestCase;

class PostTest extends TestCase
{
    use RefreshDatabase;
    use WorksWithBahaPages;

    protected function setUp(): void
    {
        parent::setUp();

        Event::fake();
    }

    /** @test */
    public function a_post_can_extract_links_manually()
    {
        $post = Post::factory()->create();

        $this->assertCount(0, Link::all());

        $post->extractLinks();

        $this->assertCount(1, Link::all());
    }

    /** @test */
    public function it_skips_quoted_string_in_content_when_extract_links()
    {
        $post = Post::factory()->create([
            'content' => $this->quotedContent(),
        ]);

        $post->extractLinks();

        $this->assertCount(5, $post->links);
    }
}
