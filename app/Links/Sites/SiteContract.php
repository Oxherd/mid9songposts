<?php

namespace App\Links\Sites;

use App\Links\UrlString;

abstract class SiteContract
{
    protected $url;

    /**
     * @param App\Links\UrlString|string $url
     */
    public function __construct($url)
    {
        $this->url = $url instanceof UrlString ? $url : new UrlString($url);
    }

    /**
     * the name of this site
     */
    abstract public function name();

    /**
     * get url resource id from given \App\Links\UrlString
     */
    abstract public function getResourceId();

    /**
     * using given resource id to generate a general url string
     */
    abstract public static function generalUrl($resource_id);
}
