<?php

namespace App\Links\Sites;

use App\Links\UrlString;

class BahaRedirect extends SiteContract
{
    /**
     * cache real target Site class, avoid additional instance create
     *
     * @property \App\Links\Sites\SiteContract
     */
    protected $realSite;

    /**
     * extract real target url from query string
     * and create and cache real target SiteFactory
     */
    public function __construct($url)
    {
        parent::__construct($url);

        $urlParam = $this->url->query('url');

        $this->url = new UrlString(urldecode($urlParam));

        $this->realSite = (new SiteFactory($this->url))->create();
    }

    /**
     * using real target url to initiate a new Site instance and get site name
     *
     * @return string
     */
    public function name()
    {
        return $this->realSite->name();
    }

    /**
     * using real target url to initiate a new Site instance and get resource id
     *
     * @return string|null
     */
    public function getResourceId()
    {
        return $this->realSite->getResourceId();
    }
}
