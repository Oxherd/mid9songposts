<?php

namespace Tests\Unit\Baha;

use App\Baha\SearchTitle;
use App\Exceptions\NotExpectedPageException;
use App\Jobs\ScrapeBahaPostsContinuously;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Queue;
use Tests\TestCase;

class SearchTitleTest extends TestCase
{
    /** @test */
    public function given_url_must_be_expected_url_string()
    {
        $this->expectException(NotExpectedPageException::class);

        new SearchTitle('https://forum.gamer.com.tw/Bo.php?bsn=60076&qt=123');
    }

    /** @test */
    public function it_will_throw_exception_if_target_class_tag_in_search_title_page()
    {
        $this->expectException(NotExpectedPageException::class);

        $this->mockClientWithSearchTitleClassTagChanged();

        new SearchTitle('https://forum.gamer.com.tw/B.php?bsn=60076&qt=1&q=foo');
    }

    /** @test */
    public function it_wont_treat_no_result_as_an_error_that_need_throw_some_exception()
    {
        $this->mockClientWithSearchTitleNoResult();

        $searchTitle = new SearchTitle('https://forum.gamer.com.tw/B.php?bsn=60076&qt=1&q=foo');

        $this->assertCount(0, $searchTitle->getLinks());
    }

    /** @test */
    public function it_can_get_all_searchable_result_as_link()
    {
        $this->mockClientWithSearchTitleFirstPage();

        $searchTitle = new SearchTitle('https://forum.gamer.com.tw/B.php?bsn=60076&qt=1&q=foo');

        $links = $searchTitle->getLinks();

        $this->assertCount(30, $links);

        $this->assertInstanceOf(Collection::class, $links);
    }

    /** @test */
    public function it_can_filter_search_results_by_specify_user()
    {
        $this->mockClientWithSearchTitleFirstPage();

        $searchTitle = new SearchTitle('https://forum.gamer.com.tw/B.php?bsn=60076&qt=1&q=foo');

        $this->assertCount(30, $searchTitle->getLinks());

        $searchTitle->filterByUser('foobar666');

        $this->assertCount(1, $searchTitle->getLinks());
    }

    /** @test */
    public function it_can_check_there_is_more_page_after_current_page_or_not()
    {
        $this->mockClientWithSearchTitleAll2Pages();

        $searchTitle = new SearchTitle('https://forum.gamer.com.tw/B.php?bsn=60076&qt=1&q=foo');

        $this->assertTrue($searchTitle->hasNextPage());

        $searchTitle = $searchTitle->nextPage();

        $this->assertFalse($searchTitle->hasNextPage());
    }

    /** @test */
    public function it_can_create_another_new_instance_with_next_page_url()
    {
        $this->mockClientWithSearchTitleAll2Pages();

        $searchTitle = new SearchTitle('https://forum.gamer.com.tw/B.php?bsn=60076&qt=1&q=foo');

        $this->assertInstanceOf(SearchTitle::class, $searchTitle->nextPage());
    }

    /** @test */
    public function it_return_null_if_there_is_no_more_page()
    {
        $this->mockClientWithSearchTitleLastPage();

        $lastPage = new SearchTitle('https://forum.gamer.com.tw/B.php?bsn=60076&qt=1&q=foo');

        $this->assertNull($lastPage->nextPage());
    }

    /** @test */
    public function it_can_gather_result_and_dispatch_jobs_to_scrape_posts()
    {
        $this->mockClientWithSearchTitleFirstPage();

        Queue::fake();

        $searchTitle = new SearchTitle('https://forum.gamer.com.tw/B.php?bsn=60076&qt=1&q=foo');

        $searchTitle->handle();

        Queue::assertPushed(ScrapeBahaPostsContinuously::class, 30);
    }
}
