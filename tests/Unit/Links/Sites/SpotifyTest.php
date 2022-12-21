<?php

namespace Tests\Unit\Links\Sites;

use App\Links\Sites\Spotify;
use PHPUnit\Framework\TestCase;

class SpotifyTest extends TestCase
{
    /** @test */
    public function it_get_its_own_site_name()
    {
        $spotify = new Spotify('https://open.spotify.com/track/1LIbioTb3guzUzVtTEc8Fx');

        $this->assertEquals('spotify', $spotify->name());
    }

    /** @test */
    public function it_can_extract_resource_id_from_spotify_url()
    {
        $spotify = new Spotify('https://open.spotify.com/track/1LIbioTb3guzUzVtTEc8Fx');

        $this->assertEquals('1LIbioTb3guzUzVtTEc8Fx', $spotify->getResourceId());
    }

    /** @test */
    public function it_can_extract_resource_id_from_embed_url()
    {
        $spotify = new Spotify('https://open.spotify.com/embed/track/1LIbioTb3guzUzVtTEc8Fx');

        $this->assertEquals('1LIbioTb3guzUzVtTEc8Fx', $spotify->getResourceId());
    }

    /** @test */
    public function it_will_return_null_if_current_url_not_provide_music()
    {
        $spotify = new Spotify('https://open.spotify.com/collection/playlists');

        $this->assertNull($spotify->getResourceId());
    }

    /** @test */
    public function it_will_return_null_if_somehow_can_not_get_resource_id()
    {
        $generalUrl = new Spotify('https://open.spotify.com/track/');
        $embedUrl = new Spotify('https://open.spotify.com/embed/track');

        $this->assertNull($generalUrl->getResourceId());
        $this->assertNull($embedUrl->getResourceId());
    }

    /** @test */
    public function it_can_generate_a_general_url_by_provide_a_resource_id()
    {
        $this->assertEquals(
            'https://open.spotify.com/track/1LIbioTb3guzUzVtTEc8Fx',
            Spotify::generalUrl('1LIbioTb3guzUzVtTEc8Fx')
        );
    }

    /** @test */
    public function it_can_generate_a_embedded_url_by_provide_a_resource_id()
    {
        $this->assertEquals(
            'https://open.spotify.com/embed/track/1LIbioTb3guzUzVtTEc8Fx',
            Spotify::embeddedUrl('1LIbioTb3guzUzVtTEc8Fx')
        );
    }
}
