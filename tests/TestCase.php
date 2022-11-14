<?php

namespace Tests;

use App\Baha\PostSection;
use Illuminate\Foundation\Testing\LazilyRefreshDatabase;
use Illuminate\Foundation\Testing\TestCase as BaseTestCase;
use Mockery\MockInterface;
use Symfony\Component\DomCrawler\Crawler;
use Tests\Setup\Pages\WorksWithBahaPages;

abstract class TestCase extends BaseTestCase
{
    use CreatesApplication;
    use WorksWithBahaPages;
    use LazilyRefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        /** @var \App\Baha\PostSection */
        $mock = $this->partialMock(PostSection::class, function (MockInterface $mock) {
            $crawler = new Crawler($this->postSectionHtml());

            $article = $crawler->filter('.c-article__content')->html();

            $mock->shouldReceive('content')->andReturn($article);
        });

        app()->bind(PostSection::class, function () use ($mock) {
            $newMock = clone $mock;

            $newMock->setHTMLCrawler(new Crawler($this->postSectionHtml()));

            return $newMock;
        });
    }
}
