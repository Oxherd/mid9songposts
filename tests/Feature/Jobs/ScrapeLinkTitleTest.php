<?php

namespace Tests\Feature\Jobs;

use App\Jobs\ScrapeLinkTitle;
use App\Models\Link;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\Client\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Queue;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Tests\Setup\Pages\WorksWithYoutube;
use Tests\TestCase;

class ScrapeLinkTitleTest extends TestCase
{
    use RefreshDatabase;
    use WorksWithYoutube;

    /** @test */
    public function it_can_dispatch_job_to_set_Link_model_title()
    {
        $this->fakeNeverGonnaGiveYouUp();

        $link = Link::factory()->create();

        $this->assertNull($link->title);

        (new ScrapeLinkTitle($link))->handle();

        $this->assertNotNull($link->fresh()->title);
    }

    /** @test */
    public function it_will_send_http_request_to_retrieve_current_website_title()
    {
        $this->fakeNeverGonnaGiveYouUp();

        $link = Link::factory()->create();

        (new ScrapeLinkTitle($link))->handle();

        $this->assertEquals(
            'Rick Astley - Never Gonna Give You Up (Official Music Video) - YouTube',
            $link->fresh()->title
        );

        Http::assertSent(function (Request $request) use ($link) {
            return $request->url() === $link->general();
        });
    }

    /** @test */
    public function it_dispatchs_a_new_job_when_a_Link_model_is_created()
    {
        Queue::fake();

        $link = Link::factory()->create();

        Queue::assertPushed(function (ScrapeLinkTitle $job) use ($link) {
            return $job->link->id === $link->id;
        });
    }
}
