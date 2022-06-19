<?php

namespace Tests\Setup\Pages;

use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Http;

trait WorksWithYoutube
{
    protected $filePath = __DIR__ . '\html';

    protected function fakeNeverGonnaGiveYouUp()
    {
        Http::fake(function () {
            return Http::response(File::get($this->filePath . '\youtube.html'));
        });
    }
}
