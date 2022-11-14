<?php

namespace App\Baha;

use App\Links\UrlString;
use Symfony\Component\Panther\Client;

abstract class Page
{
    /**
     * @property \Symfony\Component\Panther\Client $cachedClient need keep client alive for crawler interaction
     */
    protected $cachedClient;

    /**
     * @property \Symfony\Component\Panther\DomCrawler\Crawler use for html interaction
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

        /** @var \Symfony\Component\Panther\Client */
        $this->cachedClient = app(Client::class);

        $this->html = $this->cachedClient->request('GET', $this->url);
    }

    /**
     * ensure url/page is expected target before scrape content
     *
     * @throws NotExpectedPageException
     */
    abstract protected function ensureIsExpectedUrl();
}
