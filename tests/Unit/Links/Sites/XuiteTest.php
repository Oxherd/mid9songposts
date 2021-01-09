<?php

namespace Tests\Unit\Links\Sites;

use App\Links\Sites\Xuite;
use PHPUnit\Framework\TestCase;

class XuiteTest extends TestCase
{
    /** @test */
    public function it_can_get_its_site_name()
    {
        $xuite = new Xuite('https://vlog.xuite.net/play/Rkh5cXdhLTMzMDk3NTY5LmZsdg==');

        $this->assertEquals('xuite', $xuite->name());
    }

    /** @test */
    public function it_can_extract_resource_id_from_xuite_video_url()
    {
        $xuite = new Xuite('https://vlog.xuite.net/play/Rkh5cXdhLTMzMDk3NTY5LmZsdg==');

        $this->assertEquals('Rkh5cXdhLTMzMDk3NTY5LmZsdg==', $xuite->getResourceId());
    }

    /** @test */
    public function it_can_extract_resource_id_from_xuite_embed_url()
    {
        $xuite = new Xuite('https://vlog.xuite.net/embed/Rkh5cXdhLTMzMDk3NTY5LmZsdg==');

        $this->assertEquals('Rkh5cXdhLTMzMDk3NTY5LmZsdg==', $xuite->getResourceId());
    }

    /** @test */
    public function it_can_extract_resource_id_from_mobile_url()
    {
        $xuite = new Xuite('https://m.xuite.net/vlog/star57/dmw2d0hiLTEwNjIzODYuZmx2');

        $this->assertEquals('dmw2d0hiLTEwNjIzODYuZmx2', $xuite->getResourceId());
    }

    /** @test */
    public function it_will_return_null_if_somehow_can_not_retreive_resource_id()
    {
        $generalUrl = new Xuite('https://vlog.xuite.net/play');
        $embedUrl = new Xuite('https://vlog.xuite.net/embed');
        $mobileUrl = new Xuite('https://m.xuite.net/vlog/star57');

        $this->assertNull($generalUrl->getResourceId());
        $this->assertNull($embedUrl->getResourceId());
        $this->assertNull($mobileUrl->getResourceId());
    }

    /** @test */
    public function it_can_generate_a_general_url_by_provide_a_resource_id()
    {
        $this->assertEquals(
            'https://vlog.xuite.net/play/Rkh5cXdhLTMzMDk3NTY5LmZsdg==',
            Xuite::generalUrl('Rkh5cXdhLTMzMDk3NTY5LmZsdg==')
        );
    }
}
