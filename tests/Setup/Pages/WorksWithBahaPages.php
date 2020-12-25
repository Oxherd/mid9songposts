<?php

namespace Tests\Setup\Pages;

use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Http;

trait WorksWithBahaPages
{
    protected $bahaUrl = 'https://forum.gamer.com.tw/C.php?bsn=60076&snA=6004847';

    protected $singlePostUrl = 'https://forum.gamer.com.tw/Co.php?bsn=60076&sn=38564976';

    protected $pagesFilePath = __DIR__ . '\html';

    protected function fakePageOnePostsResponse()
    {
        Http::fake(function ($request) {
            return Http::response(File::get($this->pagesFilePath . '\thread_p1.html'), 200);
        });
    }

    protected function fakeThreadResponse()
    {
        Http::fakeSequence()
            ->push(File::get($this->pagesFilePath . '\thread_p1.html'), 200)
            ->push(File::get($this->pagesFilePath . '\thread_p2.html'), 200)
            ->push(File::get($this->pagesFilePath . '\thread_p3.html'), 200);
    }

    protected function fakeSinglePostResponse()
    {
        Http::fake(function ($request) {
            return Http::response(File::get($this->pagesFilePath . '\single_post.html', 200));
        });
    }

    protected function postSectionHtml()
    {
        return File::get($this->pagesFilePath . '\post_section.html');
    }
}
