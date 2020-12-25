<?php

namespace Tests\Unit\Links;

use App\Links\UrlString;
use PharIo\Manifest\InvalidUrlException;
use PHPUnit\Framework\TestCase;

class UrlStringTest extends TestCase
{
    protected $url = 'https://example.foo.bar/path.php?page=2';

    /** @test */
    public function it_needs_provide_a_valid_url_to_create_a_instance()
    {
        $this->expectException(InvalidUrlException::class);

        new UrlString('random_string');
    }

    /** @test */
    public function it_can_extract_url_domain()
    {
        $url = new UrlString($this->url);

        $this->assertEquals('example.foo.bar', $url->domain());
    }

    /** @test */
    public function it_can_get_param_from_qeury_string()
    {
        $url = new UrlString($this->url);

        $this->assertEquals('2', $url->query('page'));
    }

    /** @test */
    public function it_can_check_param_exists_in_query_string_or_not()
    {
        $url = new UrlString($this->url);

        $this->assertTrue($url->hasQuery('page'));

        $this->assertFalse($url->hasQuery('NOT_EXISTS'));
    }

    /** @test */
    public function it_can_get_path_string_from_url()
    {
        $url = new UrlString($this->url);

        $this->assertEquals('/path.php', $url->path());

        $noPath = new UrlString('http://example.foo.bar');

        $this->assertEmpty($noPath->path());
    }

    /** @test */
    public function it_can_get_next_page_url()
    {
        $url = new UrlString($this->url);

        $this->assertEquals('https://example.foo.bar/path.php?page=3', $url->nextPage());
    }

    /** @test */
    public function it_auto_add_paginator_if_url_does_not_have_one()
    {
        $url = new UrlString('https://example.foo.bar');

        $this->assertEquals('https://example.foo.bar?page=2', $url->nextPage());
    }

    /** @test */
    public function it_can_stringigy()
    {
        $object = new UrlString($this->url);

        $this->assertEquals($this->url, (string) $object);
    }
}
