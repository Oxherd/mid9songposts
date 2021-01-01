<?php

namespace Tests\Unit\Links\Sites;

use App\Links\Sites\BahaRedirect;
use PHPUnit\Framework\TestCase;

class BahaRedirectTest extends TestCase
{
    /** @test */
    public function it_return_a_site_name_extract_from_query_string_url_param()
    {
        $bahaRedirect = new BahaRedirect(
            'https://ref.gamer.com.tw/redir.php?url=https%3A%2F%2Fwww.youtube.com%2Fwatch%3Fv%3DNP5xYlB8dyw'
        );

        $this->assertEquals('youtube', $bahaRedirect->name());
    }

    /** @test */
    public function it_return_a_resource_id_extract_from_query_string_url_param()
    {
        $bahaRedirect = new BahaRedirect(
            'https://ref.gamer.com.tw/redir.php?url=https%3A%2F%2Fwww.youtube.com%2Fwatch%3Fv%3DNP5xYlB8dyw'
        );

        $this->assertEquals('NP5xYlB8dyw', $bahaRedirect->getResourceId());
    }
}
