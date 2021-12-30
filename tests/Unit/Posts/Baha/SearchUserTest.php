<?php

namespace Tests\Unit\Posts\Baha;

use App\Exceptions\NotExpectedPageException;
use App\Jobs\ScrapeBahaPosts;
use App\Posts\Baha\SearchUser;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Queue;
use Tests\Setup\Pages\WorksWithBahaPages;
use Tests\TestCase;

class SearchUserTest extends TestCase
{
    use WorksWithBahaPages;

    /** @test */
    public function it_send_a_http_request_to_create_a_new_instance()
    {
        $this->fakeOnePageSearchUserResponse();

        new SearchUser('https://forum.gamer.com.tw/Bo.php?bsn=60076&qt=6&q=foobar666');

        Http::assertSent(function ($request) {
            return $request->url() === 'https://forum.gamer.com.tw/Bo.php?bsn=60076&qt=6&q=foobar666';
        });
    }

    /** @test */
    public function given_url_must_be_expected_url_string()
    {
        $this->expectException(NotExpectedPageException::class);

        new SearchUser('https://forum.gamer.com.tw/Bo.php?bsn=60076&qt=6');
    }

    /** @test */
    public function it_will_throw_exception_if_css_selector_can_not_find_corresponsive_result_in_current_page()
    {
        $this->expectException(NotExpectedPageException::class);

        $this->fakeChangedSearchUserPageResponse();

        new SearchUser('https://forum.gamer.com.tw/Bo.php?bsn=60076&qt=6&q=foobar666');
    }

    /** @test */
    public function it_wont_treat_no_result_as_an_error_that_need_throw_some_exception()
    {
        $this->fakeSearchUserNoResultResponse();

        $searchUser = new SearchUser('https://forum.gamer.com.tw/Bo.php?bsn=60076&qt=6&q=foobar666');

        $this->assertCount(0, $searchUser->getLinks());
    }

    /** @test */
    public function it_can_get_all_searchable_result_as_link_in_string_format()
    {
        $this->fakeOnePageSearchUserResponse();

        $searchUser = new SearchUser('https://forum.gamer.com.tw/Bo.php?bsn=60076&qt=6&q=foobar666');

        $this->assertCount(30, $searchUser->getLinks());
    }

    /** @test */
    public function it_can_filter_seached_result_by_provide_key_word()
    {
        $this->fakeOnePageSearchUserResponse();

        $searchUser = new SearchUser('https://forum.gamer.com.tw/Bo.php?bsn=60076&qt=6&q=foobar666');

        $this->assertCount(30, $searchUser->getLinks());

        $searchUser->filter('半夜歌串一人一首');

        $this->assertCount(29, $searchUser->getLinks());
    }

    /** @test */
    public function it_can_check_there_is_more_page_after_current_page_or_not()
    {
        $this->fakeAllPageSearchUserResponse();

        $searchUser = new SearchUser('https://forum.gamer.com.tw/Bo.php?bsn=60076&qt=6&q=foobar666');

        $this->assertTrue($searchUser->hasNextPage());

        $searchUser = $searchUser->nextPage();

        $this->assertFalse($searchUser->hasNextPage());
    }

    /** @test */
    public function it_can_create_another_new_instance_with_next_page_url()
    {
        $this->fakeAllPageSearchUserResponse();

        $searchUser = new SearchUser('https://forum.gamer.com.tw/Bo.php?bsn=60076&qt=6&q=foobar666');

        $this->assertInstanceOf(SearchUser::class, $searchUser->nextPage());
    }

    /** @test */
    public function it_return_null_if_there_is_no_more_page()
    {
        $this->fakeLastSearchUserPageResponse();

        $lastPage = new SearchUser('https://forum.gamer.com.tw/Bo.php?bsn=60076&qt=6&q=foobar666');

        $this->assertNull($lastPage->nextPage());
    }

    /** @test */
    public function it_can_gather_result_and_dispatch_jobs_to_scrape_posts()
    {
        $this->fakeOnePageSearchUserResponse();

        Queue::fake();

        $searchUser = new SearchUser('https://forum.gamer.com.tw/Bo.php?bsn=60076&qt=6&q=foobar666');

        $searchUser->handle('半夜歌串一人一首');

        Queue::assertPushed(ScrapeBahaPosts::class, 29);
    }
}
