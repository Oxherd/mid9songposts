<?php

namespace Tests\Unit;

use GuzzleHttp\Client;
use Symfony\Component\Panther\Client as PantherClient;
use Tests\TestCase;

class ClientTest extends TestCase
{
    /** @test */
    public function it_resolve_a_guzzle_client_from_container()
    {
        config(['app.scrape_by' => 'guzzle']);

        $this->assertInstanceOf(Client::class, app(Client::class));
    }

    /** @test */
    public function it_resolve_a_symfony_panther_client_from_container_if_app_config_told_to()
    {
        config(['app.scrape_by' => 'panther']);

        $this->assertInstanceOf(PantherClient::class, app(Client::class));
    }
}
