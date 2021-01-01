<?php

namespace Tests\Feature\Events;

use App\Events\PostCreated;
use App\Models\Post;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Event;
use Tests\TestCase;

class PostCreatedTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function it_will_dispatch_a_event_when_a_post_was_created()
    {
        Event::fake([
            PostCreated::class,
        ]);

        Post::factory()->forPoster()->create();

        Event::assertDispatched(PostCreated::class);
    }
}
