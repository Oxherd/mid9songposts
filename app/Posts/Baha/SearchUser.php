<?php

namespace App\Posts\Baha;

use App\Exceptions\NotExpectedPageException;
use App\Jobs\ScrapeBahaPosts;
use App\Links\UrlString;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;
use InvalidArgumentException;
use Symfony\Component\DomCrawler\Crawler;

class SearchUser
{
    /**
     * @property UrlString delegate UrlString to get url params
     */
    protected $url;

    /**
     * @property Crawler use for html interaction
     */
    protected $html;

    /**
     * store all searched result
     *
     * @property array
     */
    protected $lists;

    /**
     * it scrape page for further usage from given url
     *
     * then extract all searchable result
     *
     * @param string $url
     */
    public function __construct($url)
    {
        $this->url = $url instanceof UrlString ? $url : new UrlString($url);

        $this->ensureIsExpectedUrl();

        $this->html = new Crawler((string) Http::get((string) $this->url));

        $this->lists = $this->getResultList();
    }

    /**
     * get all available links and dispatch corresponsive job to scrape posts
     */
    public function handle($title)
    {
        $this->filter($title)
            ->getLinks()
            ->each(function ($link) {
                ScrapeBahaPosts::dispatch($link);
            });
    }

    /**
     * return all links from searchable result
     *
     * @return \Illuminate\Support\Collection wrap links array into Collection
     */
    public function getLinks()
    {
        $links = $this->lists
            ->each(function (Crawler $node) {
                $path = $node->attr('href');

                return "https://forum.gamer.com.tw/{$path}";
            });

        return Collection::make($links);
    }

    /**
     * filter down the searched result with target key word
     *
     * @param string $target the key word from thread title
     *
     * @return \App\Posts\Baha\SearchUser
     */
    public function filter($target)
    {
        $this->lists = $this->lists
            ->reduce(function (Crawler $node) use ($target) {
                return Str::contains($node->text(''), $target);
            });

        return $this;
    }

    /**
     * get a new instance with next page url if there has one
     *
     * @return \App\Posts\Baha\SearchUser|null
     */
    public function nextPage()
    {
        return $this->hasNextPage() ? new self($this->url->nextPage()) : null;
    }

    /**
     * ensure url/page is expected target in order to fetch data correctly
     *
     * @throws NotExpectedPageException
     */
    protected function ensureIsExpectedUrl()
    {
        if (
            $this->url->domain() === 'forum.gamer.com.tw' &&
            $this->url->path() === '/Bo.php' &&
            $this->url->hasQuery('q') &&
            $this->url->hasQuery('qt')
        ) {
            return;
        }

        throw new NotExpectedPageException();
    }

    /**
     * extract all searchable result from scraped html
     *
     * @return \Symfony\Component\DomCrawler\Crawler
     */
    protected function getResultList()
    {
        $lists = $this->html->filter('.b-list__main > a');

        if (
            !$lists->count() &&
            !Str::contains($this->html->text(), '很抱歉，無法搜尋到有關')
        ) {
            throw new NotExpectedPageException('Has the html structure or CSS changed?');
        }

        return $lists;
    }

    /**
     * check is there has more page can go or not
     *
     * @return bool
     */
    public function hasNextPage()
    {
        try {
            return !!$this->html->filter('.pagenow')->first()->nextAll()->text('');
        } catch (InvalidArgumentException $e) {
            return false;
        }
    }
}
