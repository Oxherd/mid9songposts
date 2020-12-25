<?php

namespace App\Links;

use League\Uri\Components\Domain;
use League\Uri\Components\Path;
use League\Uri\Components\Query;
use League\Uri\Uri;
use League\Uri\UriModifier;
use PharIo\Manifest\InvalidUrlException;

class UrlString
{
    /**
     * @var Uri for uri component interact
     */
    protected $uri;

    /**
     * @param string $url
     */
    public function __construct($url)
    {
        $this->ensureIsValidUrl($url);

        $this->uri = Uri::createFromString($url);
    }

    /**
     * extract domain from uri property
     *
     * @return string
     */
    public function domain()
    {
        return Domain::createFromUri($this->uri)->getContent();
    }

    /**
     * getting query param by given key
     *
     * @param string $key
     *
     * @return string|null
     */
    public function query($key)
    {
        return Query::createFromUri($this->uri)->get($key);
    }

    /**
     * check given param key exists in query string or not
     *
     * @return bool
     */
    public function hasQuery($key)
    {
        return Query::createFromUri($this->uri)->has($key);
    }

    /**
     * getting path string from url
     *
     * @return string
     */
    public function path()
    {
        return Path::createFromUri($this->uri)->getContent();
    }

    /**
     * generate a 'next page' uri string by append a new paginator number
     *
     * @param string $paginator in case different 'page' name
     *
     * @var int|string $number determine what number next page is
     *
     * @return UriInterface|string
     */
    public function nextPage($paginator = 'page')
    {
        $number = $this->hasQuery($paginator) ?
        $this->query($paginator) + 1 : 2;

        return UriModifier::mergeQuery($this->uri, "{$paginator}={$number}");
    }

    /**
     * @param string $url
     *
     * @throws InvalidUrlException
     */
    protected function ensureIsValidUrl($url)
    {
        if (filter_var($url, FILTER_VALIDATE_URL) === false) {
            throw new InvalidUrlException();
        }
    }

    public function __toString()
    {
        return (string) $this->uri;
    }
}
