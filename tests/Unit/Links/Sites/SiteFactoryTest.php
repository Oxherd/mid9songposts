<?php

namespace Tests\Unit\Links\Sites;

use App\Links\Sites\BahaRedirect;
use App\Links\Sites\Bilibili;
use App\Links\Sites\GoogleDrive;
use App\Links\Sites\Niconico;
use App\Links\Sites\NotRegisted;
use App\Links\Sites\SiteFactory;
use App\Links\Sites\SoundCloud;
use App\Links\Sites\Spotify;
use App\Links\Sites\ToSpotify;
use App\Links\Sites\Xuite;
use App\Links\Sites\Youtube;
use PHPUnit\Framework\TestCase;

class SiteFactoryTest extends TestCase
{
    /** @test */
    public function it_can_create_BahaRedirect_site_instance()
    {
        $siteFactory = new SiteFactory('https://ref.gamer.com.tw/redir.php?url=https%3A%2F%2Fwww.youtube.com%2Fwatch%3Fv%3DNP5xYlB8dyw');

        $this->assertInstanceOf(BahaRedirect::class, $siteFactory->create());
    }

    /** @test */
    public function it_can_create_Bilibili_site_instance()
    {
        $generalUrl = new SiteFactory('https://www.bilibili.com/video/BV1Gy4y167jL');
        $embedUrl = new SiteFactory('https://player.bilibili.com/player.html?bvid=BV1Gy4y167jL');

        $this->assertInstanceOf(Bilibili::class, $generalUrl->create());
        $this->assertInstanceOf(Bilibili::class, $embedUrl->create());
    }

    /** @test */
    public function it_can_create_GoogleDrive_site_instance()
    {
        $siteFactory = new SiteFactory('https://drive.google.com/file/d/1qjHMlN0coKQUv0TWPL3nyaiiQ2gzZLfW/view');

        $this->assertInstanceOf(GoogleDrive::class, $siteFactory->create());
    }

    /** @test */
    public function it_can_create_Niconico_site_instance()
    {
        $generalUrl = new SiteFactory('https://www.nicovideo.jp/watch/sm38041901');
        $embedUrl = new SiteFactory('https://embed.nicovideo.jp/watch/sm38041901');

        $this->assertInstanceOf(Niconico::class, $generalUrl->create());
        $this->assertInstanceOf(Niconico::class, $embedUrl->create());
    }

    /** @test */
    public function it_can_create_SoundCloud_site_instance()
    {
        $siteFactory = new SiteFactory('https://soundcloud.com/bustre/bustre-calamity');

        $this->assertInstanceOf(SoundCloud::class, $siteFactory->create());
    }

    /** @test */
    public function it_can_create_Spotify_site_instance()
    {
        $generalUrl = new SiteFactory('https://open.spotify.com/track/1LIbioTb3guzUzVtTEc8Fx');
        $embedUrl = new SiteFactory('https://open.spotify.com/embed/track/1LIbioTb3guzUzVtTEc8Fx');

        $this->assertInstanceOf(Spotify::class, $generalUrl->create());
        $this->assertInstanceOf(Spotify::class, $embedUrl->create());
    }

    /** @test */
    public function it_can_create_ToSpotify_site_instance()
    {
        $siteFactory = new SiteFactory('https://link.tospotify.com/fGu711Rupbb');

        $this->assertInstanceOf(ToSpotify::class, $siteFactory->create());
    }

    /** @test */
    public function it_can_create_Xuite_site_instance()
    {
        $siteFactory = new SiteFactory('https://vlog.xuite.net/embed/Rkh5cXdhLTMzMDk3NTY5LmZsdg==');

        $this->assertInstanceOf(Xuite::class, $siteFactory->create());
    }

    /** @test */
    public function it_can_create_a_Youtube_site_instance()
    {
        $generalUrl = new SiteFactory('https://www.youtube.com/watch?v=dQw4w9WgXcQ');
        $shortUrl = new SiteFactory('https://youtu.be/dQw4w9WgXcQ');
        $musicUrl = new SiteFactory('https://music.youtube.com/watch?v=dQw4w9WgXcQ');
        $embedUrl = new SiteFactory('https://www.youtube.com/embed/dQw4w9WgXcQ');

        $this->assertInstanceOf(Youtube::class, $generalUrl->create());
        $this->assertInstanceOf(Youtube::class, $shortUrl->create());
        $this->assertInstanceOf(Youtube::class, $musicUrl->create());
        $this->assertInstanceOf(Youtube::class, $embedUrl->create());
    }

    /** @test */
    public function it_will_create_NotRegisted_site_instance_if_given_url_domain_not_registed_in_lookup_table()
    {
        $notRegisted = new SiteFactory('https://example.foo.bar');

        $this->assertInstanceOf(NotRegisted::class, $notRegisted->create());
    }
}
