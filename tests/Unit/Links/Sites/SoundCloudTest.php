<?php

namespace Tests\Unit\Links\Sites;

use App\Links\Sites\SoundCloud;
use Tests\Setup\Pages\WorksWithSoundCloud;
use Tests\TestCase;

class SoundCloudTest extends TestCase
{
    use WorksWithSoundCloud;

    /** @test */
    public function it_can_get_its_site_name()
    {
        $soundCloud = new SoundCloud('https://soundcloud.com/bustre/bustre-calamity');

        $this->assertEquals('sound_cloud', $soundCloud->name());
    }

    /** @test */
    public function it_can_extract_url_path_and_sound_cloud_music_id_as_resource_id()
    {
        $this->fakeSoundCloudPageResponse();

        $soundCloud = new SoundCloud('https://soundcloud.com/bustre/bustre-calamity');

        $this->assertEquals('/bustre/bustre-calamity?sounds=271297238', $soundCloud->getResourceId());
    }

    /** @test */
    public function it_must_can_retreive_its_sounds_id_from_music_page_for_recognize_it_provide_music()
    {
        $this->fakeNotMusicPageResponse();

        $soundCloud = new SoundCloud('https://soundcloud.com/agamidae/sets/ambient-piano');

        $this->assertNull($soundCloud->getResourceId());
    }
}
