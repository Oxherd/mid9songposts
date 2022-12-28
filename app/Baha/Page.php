<?php

namespace App\Baha;

use App\Links\UrlString;

abstract class Page
{
    /**
     * @property \GuzzleHttp\Client|\Symfony\Component\Panther\Client $cachedClient need keep client alive for crawler interaction
     */
    protected $cachedClient;

    /**
     * @property \Symfony\Component\DomCrawler\Crawler|\Symfony\Component\Panther\DomCrawler\Crawler use for html interaction
     */
    public $html;

    /**
     * @property \App\Links\UrlString delegate UrlString to get url params
     */
    protected $url;

    /**
     * @param string|mixed $url
     */
    public function __construct($url)
    {
        $this->url = $url instanceof UrlString ? $url : new UrlString($url);

        $this->ensureIsExpectedUrl();

        $scraper = new Scraper();

        /** @var \GuzzleHttp\Client|\Symfony\Component\Panther\Client */
        $this->cachedClient = $scraper->client();

        $this->html = $scraper->getPage((string) $this->url);
    }

    /**
     * ensure url/page is expected target before scrape content
     *
     * @throws NotExpectedPageException
     */
    abstract protected function ensureIsExpectedUrl();
}
