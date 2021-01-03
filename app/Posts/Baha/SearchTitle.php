<?php

namespace App\Posts\Baha;

use App\Exceptions\NotExpectedPageException;
use App\Links\UrlString;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;
use InvalidArgumentException;
use Symfony\Component\DomCrawler\Crawler;

class SearchTitle
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
     * return all links from searchable result
     *
     * @return \Illuminate\Support\Collection wrap links array into Collection
     */
    public function getLinks()
    {
        $links = $this->lists
            ->each(function (Crawler $node) {
                $path = $node->filter('.b-list__main__title')->first()->attr('href');

                return "https://forum.gamer.com.tw/{$path}";
            });

        return Collection::make($links);
    }

    /**
     * filter down searched result by specific user
     *
     * @param string $user
     *
     * @return \App\Posts\Baha\SearchTitle
     */
    public function filterByUser($user = null)
    {
        if (!$user) {
            return $this;
        }

        $this->lists = $this->lists
            ->reduce(function (Crawler $node) use ($user) {
                return $node->filter('.b-list__count__user')->text('') === $user;
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
            $this->url->path() === '/B.php' &&
            $this->url->hasQuery('q') &&
            $this->url->query('qt') == '1'
        ) {
            return;
        }

        throw new NotExpectedPageException();
    }

    /**
     * extract all searchable result from scraped html
     *
     * add filter get rid of ad row
     *
     * @return \Symfony\Component\DomCrawler\Crawler
     */
    protected function getResultList()
    {
        return $this->html
            ->filter('.b-list__row')
            ->reduce(function (Crawler $node) {
                return !$node->filter('.b-list_ad')->text('');
            });
    }

    /**
     * check is there has more page can go or not
     *
     * @return bool
     */
    protected function hasNextPage()
    {
        try {
            return !!$this->html->filter('.pagenow')->first()->nextAll()->text('');
        } catch (InvalidArgumentException $e) {
            return false;
        }
    }
}
