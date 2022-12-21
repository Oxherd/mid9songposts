<?php

namespace Tests\Unit\Links\Sites;

use App\Links\Sites\Niconico;
use PHPUnit\Framework\TestCase;

class NiconicoTest extends TestCase
{
    /** @test */
    public function it_can_get_its_site_name()
    {
        $niconico = new Niconico('https://www.nicovideo.jp/watch/sm38041901');

        $this->assertEquals('niconico', $niconico->name());
    }

    /** @test */
    public function it_can_extract_resource_id_from_general_url()
    {
        $niconico = new Niconico('https://www.nicovideo.jp/watch/sm38041901');

        $this->assertEquals('sm38041901', $niconico->getResourceId());
    }

    /** @test */
    public function it_can_extract_resource_id_from_embed_url()
    {
        $niconico = new Niconico('https://embed.nicovideo.jp/watch/sm38041901');

        $this->assertEquals('sm38041901', $niconico->getResourceId());
    }

    /** @test */
    public function it_will_return_null_if_somehow_can_not_get_resource_id()
    {
        $niconico = new Niconico('https://embed.nicovideo.jp/watch/');

        $this->assertNull($niconico->getResourceId());
    }

    /** @test */
    public function it_can_generate_a_general_url_by_provide_a_resource_id()
    {
        $this->assertEquals(
            'https://www.nicovideo.jp/watch/sm38041901',
            Niconico::generalUrl('sm38041901')
        );
    }

    /** @test */
    public function it_can_generate_a_embedded_url_by_provide_a_resource_id()
    {
        $this->assertEquals(
            'https://embed.nicovideo.jp/watch/sm38041901',
            Niconico::embeddedUrl('sm38041901')
        );
    }
}
