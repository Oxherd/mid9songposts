<?php

namespace Tests\Unit\Posts\Baha;

use App\Exceptions\NotExpectedPageException;
use Tests\TestCase;
use App\Posts\Baha\SearchUser;
use Illuminate\Support\Facades\Http;
use Tests\Setup\Pages\WorksWithBahaPages;

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
    public function it_can_create_another_new_instance_with_next_page_url()
    {
        $this->fakeAllPageSearchUserResponse();

        $searchUser = new SearchUser('https://forum.gamer.com.tw/Bo.php?bsn=60076&qt=6&q=foobar666');

        $this->assertInstanceOf(SearchUser::class, $searchUser->nextPage());
    }

    /** @test */
    public function it_return_null_if_there_is_no_more_page()
    {
        $this->fakeOnePageSearchUserResponse();

        $searchUser = new SearchUser('https://forum.gamer.com.tw/Bo.php?bsn=60076&qt=6&q=foobar666');

        $noMore = $searchUser->nextPage();

        $this->assertNull($noMore->nextPage());
    }
}
