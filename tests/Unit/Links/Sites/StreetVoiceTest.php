<?php

namespace Tests\Unit\Links\Sites;

use App\Links\Sites\StreetVoice;
use PHPUnit\Framework\TestCase;

class StreetVoiceTest extends TestCase
{
    /** @test */
    public function it_can_get_its_own_site_name()
    {
        $streetVoice = new StreetVoice('https://streetvoice.com/tryingtimes/songs/562857');

        $this->assertEquals('street_voice', $streetVoice->name());
    }

    /** @test */
    public function it_can_extract_resource_id_from_general_url()
    {
        $streetVoice = new StreetVoice('https://streetvoice.com/tryingtimes/songs/562857');

        $this->assertEquals('tryingtimes/songs/562857', $streetVoice->getResourceId());
    }

    /** @test */
    public function it_return_null_if_resource_id_not_a_valid_format()
    {
        $streetVoice = new StreetVoice('https://streetvoice.com/tryingtimes/songs');

        $this->assertNull($streetVoice->getResourceId());
    }

    /** @test */
    public function it_can_generate_a_general_url_by_provide_a_resource_id()
    {
        $this->assertEquals(
            'https://streetvoice.com/tryingtimes/songs/562857',
            StreetVoice::generalUrl('tryingtimes/songs/562857')
        );
    }

    /** @test */
    public function it_can_generate_a_embedded_url_by_provide_a_resource_id()
    {
        $this->assertEquals(
            'https://streetvoice.com/music/embed/?id=562857',
            StreetVoice::embeddedUrl('tryingtimes/songs/562857')
        );
    }
}
