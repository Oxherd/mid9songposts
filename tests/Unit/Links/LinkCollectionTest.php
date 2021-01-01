<?php

namespace Tests\Unit\Links;

use App\Links\LinkCollection;
use App\Models\Link;
use Illuminate\Support\Collection;
use Tests\TestCase;

class LinkCollectionTest extends TestCase
{
    /** @test */
    public function it_use_static_fromText_method_to_create_a_new_instance()
    {
        $this->assertInstanceOf(LinkCollection::class, LinkCollection::fromText(''));
    }

    /** @test */
    public function it_can_retrieve_links_property()
    {
        $links = LinkCollection::fromText('')->get();

        $this->assertInstanceOf(Collection::class, $links);
    }

    /** @test */
    public function it_convert_url_string_into_Link_model()
    {
        $links = LinkCollection::fromText('https://www.youtube.com/watch?v=dQw4w9WgXcQ')->get();

        $this->assertInstanceOf(Link::class, $links[0]);
    }

    /** @test */
    public function it_can_keeps_links_array_element_unique()
    {
        $links = LinkCollection::fromText("
            https://www.youtube.com/watch?v=DUPLICATED
            https://www.youtube.com/watch?v=ANOTHER
            https://www.youtube.com/watch?v=DUPLICATED
        ")
            ->unique();

        $links = $links->get();

        $this->assertCount(2, $links);
        $this->assertEquals('https://www.youtube.com/watch?v=DUPLICATED', $links[0]->original);
        $this->assertEquals('https://www.youtube.com/watch?v=ANOTHER', $links[1]->original);
    }

    /** @test */
    public function it_treat_links_as_the_same_if_domain_not_register_in_lookup_table()
    {
        $links = LinkCollection::fromText("
            https://example.foo.bar/path_1?param=foo
            https://example.foo.bar/path_2?param=bar
            https://example.foo.bar/path_3?param=baz
        ")
            ->unique();

        $this->assertCount(1, $links->get());
    }

    /** @test */
    public function it_treat_the_same_link_as_long_as_different_domain_register_same_site_in_lookup_table()
    {
        $links = LinkCollection::fromText("
            https://www.youtube.com/watch?v=dQw4w9WgXcQ
            https://youtu.be/dQw4w9WgXcQ
        ")
            ->unique();

        $this->assertCount(1, $links->get());
    }

    /** @test */
    public function it_filter_down_link_that_domain_not_register_in_lookup_table()
    {
        $links = LinkCollection::fromText("
            https://example.foo.bar/path_1?param=foo
            https://example.foo.bar/path_2?param=bar
            https://example.foo.bar/path_3?param=baz
            https://youtu.be/dQw4w9WgXcQ
        ")
            ->filter();

        $this->assertCount(1, $links->get());
    }
}
