<?php

namespace Tests\Unit\Links\Sites;

use App\Links\Sites\ToSpotify;
use Illuminate\Support\Facades\Http;
use Tests\Setup\Pages\WorksWithSpotify;
use Tests\TestCase;

class ToSpotifyTest extends TestCase
{
    use WorksWithSpotify;

    /** @test */
    public function it_can_get_site_name_as_same_as_Spotify_site_instance()
    {
        $toSpotify = new ToSpotify('https://link.tospotify.com/fGu711Rupbb');

        $this->assertEquals('spotify', $toSpotify->name());
    }

    /** @test */
    public function it_will_send_http_request_to_fetch_redirect_page_html_when_get_resource_id()
    {
        $this->fakeToSpotifyResponse();

        (new ToSpotify('https://link.tospotify.com/fGu711Rupbb'))->getResourceId();

        Http::assertSent(function ($request) {
            return $request->url() === 'https://link.tospotify.com/fGu711Rupbb';
        });
    }

    /** @test */
    public function it_can_extract_resource_id_from_redirec_page()
    {
        $this->fakeToSpotifyResponse();

        $toSpotify = new ToSpotify('https://link.tospotify.com/fGu711Rupbb');

        $this->assertEquals('4qSTto2rex4IYGhqtuMkjv', $toSpotify->getResourceId());
    }
}
