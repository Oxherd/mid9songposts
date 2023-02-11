<?php

namespace Tests\Feature;

use App\Models\Link;
use App\Models\Poster;
use Tests\TestCase;

class ViewAllLinksTest extends TestCase
{
    /** @test */
    public function it_can_view_list_of_links()
    {
        $links = Link::factory(3)->create();

        $response = $this->get(route('links.index'));

        $response->assertSee($links->pluck('title')->toArray());
        $response->assertSee($links->pluck('resource_id')->toArray());
    }

    /** @test */
    public function it_can_see_who_posted_the_link()
    {
        $link = Link::factory()->create();

        $response = $this->get(route('links.index'));

        $response->assertSee($link->poster->account);
    }

    /** @test */
    public function it_can_see_when_it_posted_by_the_post_created_at()
    {
        $link = Link::factory()->create();

        $response = $this->get(route('links.index'));

        $response->assertSee($link->post->created_at);
    }

    /** @test */
    public function it_can_see_which_thread_it_posted_at()
    {
        $link = Link::factory()->create();

        $response = $this->get(route('links.index'));

        $response->assertSee($link->thread->title);
        $response->assertSee($link->thread->date);
    }

    /** @test */
    public function it_can_only_sort_by_desc_or_asc()
    {
        $response = $this->get(route('links.index', [
            'sort' => 'not desc nor asc',
        ]));

        $response->assertRedirect();

        $response = $this->get(route('links.index', [
            'sort' => 'desc',
        ]));

        $response->assertOk();

        $response = $this->get(route('links.index', [
            'sort' => 'asc',
        ]));

        $response->assertOk();
    }

    /** @test */
    public function it_shows_20_links_at_a_time()
    {
        Link::factory(21)->create();

        $response = $this->get(route('links.index'));

        $this->assertCount(20, $response->viewData('links'));
    }

    /** @test */
    public function it_can_filter_links_by_poster_account()
    {
        $John = Poster::factory()->create(['account' => 'john']);
        $David = Poster::factory()->create(['account' => 'david']);

        Link::factory()->for($John)->create(['title' => 'Posted by John.']);
        Link::factory()->for($David)->create(['title' => 'Posted by David.']);

        $response = $this->get(route('links.index', [
            'account' => 'john',
        ]));

        $response->assertSee('Posted by John');
        $response->assertDontSee('Posted by David');
    }

    /** @test */
    public function it_can_filter_links_by_search_title()
    {
        Link::factory()->create(['title' => 'ABC song.']);
        Link::factory()->create(['title' => 'Christmas carol.']);

        $response = $this->get(route('links.index', [
            'search' => 'abc',
        ]));

        $response->assertSee('ABC song');
        $response->assertDontSee('Christmas carol');
    }

    /** @test */
    public function it_has_its_own_default_page_title()
    {
        $response = $this->get(route('links.index'));

        $response->assertSee('歌曲列表 - mid9songposts', false);
    }

    /** @test */
    public function it_shows_what_account_you_are_currently_specified_in_the_title()
    {
        $response = $this->get(route('links.index', [
            'account' => 'john',
        ]));

        $response->assertSee('搜尋 帳號: john', false);
    }

    /** @test */
    public function it_shows_what_link_title_you_are_currently_searching_for_in_the_title()
    {
        $response = $this->get(route('links.index', [
            'search' => 'abc',
        ]));

        $response->assertSee('搜尋 標題: abc', false);
    }

    /** @test */
    public function it_shows_what_account_currently_specified_and_what_link_title_currently_searching_for_in_the_title_at_same_time()
    {
        $response = $this->get(route('links.index', [
            'account' => 'john',
            'search' => 'abc',
        ]));

        $response->assertSee('搜尋 john 張貼的 abc', false);
    }
}
