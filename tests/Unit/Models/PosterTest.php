<?php

namespace Tests\Unit\Models;

use App\Jobs\ScrapePostsFromSearchUserContinuously;
use App\Models\Poster;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Queue;
use Tests\TestCase;

class PosterTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function it_dispatch_ScrapePostsFromSearchUserContinuously_job_when_created()
    {
        Queue::fake();

        $poster = Poster::factory()->create();

        Queue::assertPushed(function (ScrapePostsFromSearchUserContinuously $job) use ($poster) {
            return $job->user === $poster->account;
        });
    }

    /** @test */
    public function it_has_avatar_attribute_based_on_its_account()
    {
        $poster = Poster::factory()->create(['account' => 'JohnDoe']);

        $this->assertEquals(
            "https://avatar2.bahamut.com.tw/avataruserpic/j/o/johndoe/johndoe_s.png",
            $poster->avatar
        );
    }
}
