<?php

namespace Tests\Unit\Models;

use App\Jobs\ScrapePostsFromSearchUser;
use App\Models\Poster;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Queue;
use Tests\TestCase;

class PosterTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function it_dispatch_ScrapePostsFromSearchUser_job_when_created()
    {
        Queue::fake();

        $poster = Poster::factory()->create();

        Queue::assertPushed(function (ScrapePostsFromSearchUser $job) use ($poster) {
            return $job->user === $poster->account;
        });
    }
}
