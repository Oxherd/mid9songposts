<?php

namespace Tests\Unit\Models;

use App\Models\Link;
use Tests\TestCase;

class LinkTest extends TestCase
{
    /** @test */
    public function it_set_site_attribute_when_assign_original_attribute()
    {
        $link = Link::make(['original' => 'https://www.youtube.com/watch?v=dQw4w9WgXcQ']);

        $this->assertEquals('youtube', $link->site);
    }

    /** @test */
    public function if_original_domain_not_in_domains_table__site_attribute_will_be_its_own_doamin()
    {
        $link = Link::make(['original' => 'https://example.foo.bar/video']);

        $this->assertEquals('example.foo.bar', $link->site);
    }

    /** @test */
    public function it_set_resource_attribute_when_assign_original_attribute()
    {
        $link = Link::make(['original' => 'https://www.youtube.com/watch?v=dQw4w9WgXcQ']);

        $this->assertEquals('dQw4w9WgXcQ', $link->resource_id);
    }

    /** @test */
    public function it_will_be_empty_resource_attribute_if_original_attribute_domain_not_in_lookup_table()
    {
        $link = Link::make(['original' => 'https://example.foo.bar']);

        $this->assertNull($link->resource);
    }
}
