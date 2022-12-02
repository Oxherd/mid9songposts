<?php

namespace Tests\Setup\Pages;

use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Http;

trait WorksWithSoundCloud
{
    protected $filePath = __DIR__ . '/html';

    protected function fakeSoundCloudPageResponse()
    {
        Http::fake(function () {
            return Http::response(File::get($this->filePath . '/sound_cloud_music.html'));
        });
    }

    protected function fakeNotMusicPageResponse()
    {
        Http::fake(function () {
            return Http::response(File::get($this->filePath . '/sound_cloud_not_music.html'));
        });
    }
}
