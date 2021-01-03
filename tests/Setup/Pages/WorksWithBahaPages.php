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

    protected function quotedContent()
    {
        return File::get($this->pagesFilePath . '\quoted_content.html');
    }

    protected function fakeNoDateTitleResponse()
    {
        Http::fake([
            'forum.gamer.com.tw/*' =>
            Http::response(File::get($this->pagesFilePath . '\no_date_title.html')),
        ]);
    }

    protected function fakeDifferentYearPostResponse()
    {
        Http::fake([
            'forum.gamer.com.tw/*' =>
            Http::response(File::get($this->pagesFilePath . '\different_year_post.html')),
        ]);
    }

    protected function fakeOnePageSearchUserResponse()
    {
        Http::fake([
            'forum.gamer.com.tw' => Http::fakeSequence()
                ->push(File::get($this->pagesFilePath . '\search_user_p1.html'), 200)
                ->push('', 404),
        ]);
    }

    protected function fakeAllPageSearchUserResponse()
    {
        Http::fake([
            'form.gamer.com.tw' => Http::fakeSequence()
                ->push(File::get($this->pagesFilePath . '\search_user_p1.html'), 200)
                ->push(File::get($this->pagesFilePath . '\search_user_p2.html'), 200),
        ]);
    }

    protected function fakeOnePageSearchTitleResponse()
    {
        Http::fake([
            'forum.gamer.com.tw' => Http::fakeSequence()
                ->push(File::get($this->pagesFilePath . '\search_title_p1.html'), 200)
                ->push('', 404),
        ]);
    }

    protected function fakeAllPageSearchTitleResponse()
    {
        Http::fake([
            'forum.gamer.com.tw' => Http::fakeSequence()
                ->push(File::get($this->pagesFilePath . '\search_title_p1.html'), 200)
                ->push(File::get($this->pagesFilePath . '\search_title_p2.html'), 200)
                ->push('', 404),
        ]);
    }
}
