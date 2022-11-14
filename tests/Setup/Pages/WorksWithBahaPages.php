<?php

namespace Tests\Setup\Pages;

use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Http;
use Mockery;
use Mockery\MockInterface;
use Symfony\Component\BrowserKit\AbstractBrowser;
use Symfony\Component\DomCrawler\Crawler;
use Symfony\Component\Panther\Client;

trait WorksWithBahaPages
{
    protected $bahaUrl = 'https://forum.gamer.com.tw/C.php?bsn=60076&snA=6004847';

    protected $singlePostUrl = 'https://forum.gamer.com.tw/Co.php?bsn=60076&sn=38564976';

    protected $pagesFilePath = __DIR__ . '\html';

    protected $jsonTextPath = __DIR__ . '\json';

    protected function postSectionHtml()
    {
        return File::get($this->pagesFilePath . '\post_section.html');
    }

    protected function fakeFetchSingleCommentResponse()
    {
        Http::fake(function () {
            return Http::response(File::get($this->jsonTextPath . '\single_comment.json'), 200);
        });
    }

    protected function fakeFetchMultipleCommentsResponse()
    {
        Http::fake(function () {
            return Http::response(File::get($this->jsonTextPath . '\multiple_comments.json'), 200);
        });
    }

    protected function mockClientWithSinglePost()
    {
        app()->instance(Client::class, Mockery::mock(AbstractBrowser::class, function (MockInterface $mock) {
            $singlePostHTML = File::get($this->pagesFilePath . '\single_post.html');

            $mock->shouldReceive('request')->andReturn(new Crawler($singlePostHTML));
            $mock->shouldReceive('getTitle')->andReturn('RE:【情報】12/8 半夜歌串一人一首 @場外休憩區 哈啦板 - 巴哈姆特');
        }));
    }

    protected function mockClientWithThreadFirstPage()
    {
        app()->instance(Client::class, Mockery::mock(AbstractBrowser::class, function (MockInterface $mock) {
            $threadHTML = File::get($this->pagesFilePath . '\thread_p1.html');

            $mock->shouldReceive('request')->andReturn(new Crawler($threadHTML));
            $mock->shouldReceive('getTitle')->andReturn('【情報】11/7 半夜歌串一人一首 @場外休憩區 哈啦板 - 巴哈姆特');
        }));
    }

    protected function mockClientWithThreadUnavailable()
    {
        app()->instance(Client::class, Mockery::mock(AbstractBrowser::class, function (MockInterface $mock) {
            $unavailableHTML = File::get($this->pagesFilePath . '\thread_unavailable.html');

            $mock->shouldReceive('request')->andReturn(new Crawler($unavailableHTML));
        }));
    }

    protected function mockClientWithThreadNoDateInTitle()
    {
        app()->instance(Client::class, Mockery::mock(AbstractBrowser::class, function (MockInterface $mock) {
            $unavailableHTML = File::get($this->pagesFilePath . '\no_date_title.html');

            $mock->shouldReceive('request')->andReturn(new Crawler($unavailableHTML));
            $mock->shouldReceive('getTitle')->andReturn('【情報】. @場外休憩區 哈啦板 - 巴哈姆特');
        }));
    }

    protected function mockClientWithSinglePostFromDifferentYear()
    {
        app()->instance(Client::class, Mockery::mock(AbstractBrowser::class, function (MockInterface $mock) {
            $unavailableHTML = File::get($this->pagesFilePath . '\different_year_post.html');

            $mock->shouldReceive('request')->andReturn(new Crawler($unavailableHTML));
            $mock->shouldReceive('getTitle')->andReturn('RE:【情報】12/31 半夜歌串一人一首 @場外休憩區 哈啦板 - 巴哈姆特');
        }));
    }

    protected function mockClientWithThreadAll3Pages()
    {
        app()->instance(Client::class, Mockery::mock(AbstractBrowser::class, function (MockInterface $mock) {
            $threadP1 = File::get($this->pagesFilePath . '\thread_p1.html');
            $threadP2 = File::get($this->pagesFilePath . '\thread_p2.html');
            $threadP3 = File::get($this->pagesFilePath . '\thread_p3.html');

            $mock->shouldReceive('request')->andReturns(
                new Crawler($threadP1),
                new Crawler($threadP2),
                new Crawler($threadP3)
            );
        }));
    }

    protected function mockClientWithSearchUserPageClassTagChanged()
    {
        app()->instance(Client::class, Mockery::mock(AbstractBrowser::class, function (MockInterface $mock) {
            $html = File::get($this->pagesFilePath . '\search_user_p1.html');

            $classTagChanged = str_replace('b-list__main', 'changed_class', $html);

            $mock->shouldReceive('request')->andReturn(new Crawler($classTagChanged));
        }));
    }

    protected function mockClientWithSearchUserNoResult()
    {
        app()->instance(Client::class, Mockery::mock(AbstractBrowser::class, function (MockInterface $mock) {
            $noResult = File::get($this->pagesFilePath . '\search_user_no_result.html');

            $mock->shouldReceive('request')->andReturn(new Crawler($noResult));
        }));
    }

    protected function mockClientWithSearchUserFirstPage()
    {
        app()->instance(Client::class, Mockery::mock(AbstractBrowser::class, function (MockInterface $mock) {
            $searchUserP1 = File::get($this->pagesFilePath . '\search_user_p1.html');

            $mock->shouldReceive('request')->andReturn(new Crawler($searchUserP1));
        }));
    }

    protected function mockClientWithSearchUserAll2Pages()
    {
        app()->instance(Client::class, Mockery::mock(AbstractBrowser::class, function (MockInterface $mock) {
            $searchUserP1 = File::get($this->pagesFilePath . '\search_user_p1.html');
            $searchUserP2 = File::get($this->pagesFilePath . '\search_user_p2.html');

            $mock->shouldReceive('request')->andReturns(
                new Crawler($searchUserP1),
                new Crawler($searchUserP2)
            );
        }));
    }

    protected function mockClientWithSearchUserLastPage()
    {
        app()->instance(Client::class, Mockery::mock(AbstractBrowser::class, function (MockInterface $mock) {
            $lastPage = File::get($this->pagesFilePath . '\search_user_p2.html');

            $mock->shouldReceive('request')->andReturn(new Crawler($lastPage));
        }));
    }

    protected function mockClientWithSearchTitleFirstPage()
    {
        app()->instance(Client::class, Mockery::mock(AbstractBrowser::class, function (MockInterface $mock) {
            $searchTitleP1 = File::get($this->pagesFilePath . '\search_title_p1.html');

            $mock->shouldReceive('request')->andReturn(new Crawler($searchTitleP1));
        }));
    }

    protected function mockClientWithSearchTitleClassTagChanged()
    {
        app()->instance(Client::class, Mockery::mock(AbstractBrowser::class, function (MockInterface $mock) {
            $html = File::get($this->pagesFilePath . '\search_title_p1.html');

            $classTagChanged = str_replace('b-list-item', 'changed_class', $html);

            $mock->shouldReceive('request')->andReturn(new Crawler($classTagChanged));
        }));
    }

    protected function mockClientWithSearchTitleNoResult()
    {
        app()->instance(Client::class, Mockery::mock(AbstractBrowser::class, function (MockInterface $mock) {
            $noResult = File::get($this->pagesFilePath . '\search_title_no_result.html');

            $mock->shouldReceive('request')->andReturn(new Crawler($noResult));
        }));
    }

    protected function mockClientWithSearchTitleAll2Pages()
    {
        app()->instance(Client::class, Mockery::mock(AbstractBrowser::class, function (MockInterface $mock) {
            $searchTitleP1 = File::get($this->pagesFilePath . '\search_title_p1.html');
            $searchTitleP2 = File::get($this->pagesFilePath . '\search_title_p2.html');

            $mock->shouldReceive('request')->andReturns(
                new Crawler($searchTitleP1),
                new Crawler($searchTitleP2)
            );
        }));
    }

    protected function mockClientWithSearchTitleLastPage()
    {
        app()->instance(Client::class, Mockery::mock(AbstractBrowser::class, function (MockInterface $mock) {
            $lastPage = File::get($this->pagesFilePath . '\search_title_p2.html');

            $mock->shouldReceive('request')->andReturn(new Crawler($lastPage));
        }));
    }
}
