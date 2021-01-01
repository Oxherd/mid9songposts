<?php

namespace Tests\Unit\Links\Sites;

use App\Links\Sites\Bilibili;
use PHPUnit\Framework\TestCase;

class BilibiliTest extends TestCase
{
    /** @test */
    public function it_can_get_its_nick_name()
    {
        $bilibili = new Bilibili('https://www.bilibili.com/video/BV1Gy4y167jL');

        $this->assertEquals('bilibili', $bilibili->name());
    }

    /** @test */
    public function it_can_extract_resource_id_from_general_url()
    {
        $bilibili = new Bilibili('https://www.bilibili.com/video/BV1Gy4y167jL');

        $this->assertEquals('BV1Gy4y167jL', $bilibili->getResourceId());
    }

    /** @test */
    public function it_can_extract_resource_id_from_embed_url()
    {
        $bilibili = new Bilibili('https://player.bilibili.com/player.html?bvid=BV1Gy4y167jL');

        $this->assertEquals('BV1Gy4y167jL', $bilibili->getResourceId());
    }

    /** @test */
    public function it_will_return_null_if_given_url_can_not_get_resource_id()
    {
        $generalUrl = new Bilibili('https://www.bilibili.com/video/');
        $embedUrl = new Bilibili('https://player.bilibili.com/player.html');

        $this->assertNull($generalUrl->getResourceId());
        $this->assertNull($embedUrl->getResourceId());
    }
}
