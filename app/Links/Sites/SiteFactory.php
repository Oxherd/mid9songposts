<?php

namespace App\Links\Sites;

use App\Links\UrlString;

class SiteFactory
{
    /**
     * a lookup table that describe a site can has many domain
     *
     * @property array
     */
    protected $domainsTable = [
        'baha_redirect' => [
            'ref.gamer.com.tw',
        ],

        'bilibili' => [
            'www.bilibili.com',
            'player.bilibili.com',
        ],

        'google_drive' => [
            'drive.google.com',
        ],

        'niconico' => [
            'www.nicovideo.jp',
            'embed.nicovideo.jp',
        ],

        'sound_cloud' => [
            'soundcloud.com',
        ],

        'spotify' => [
            'open.spotify.com',
        ],

        'to_spotify' => [
            'link.tospotify.com',
        ],

        'xuite' => [
            'vlog.xuite.net',
        ],

        'youtube' => [
            'www.youtube.com',
            'youtu.be',
            'music.youtube.com',
        ],
    ];

    /**
     * a lookup table that determin which site become which associated class
     *
     * @property array
     */
    protected $siteClasses = [
        'baha_redirect' => BahaRedirect::class,
        'bilibili' => Bilibili::class,
        'google_drive' => GoogleDrive::class,
        'niconico' => Niconico::class,
        'sound_cloud' => SoundCloud::class,
        'spotify' => Spotify::class,
        'to_spotify' => ToSpotify::class,
        'xuite' => Xuite::class,
        'youtube' => Youtube::class,
    ];

    /**
     * @property \App\Links\UrlString
     */
    protected $url;

    public function __construct($url)
    {
        $this->url = $url instanceof UrlString ? $url : new UrlString($url);
    }

    /**
     * delegate to create a new site instance
     *
     * @return \App\Links\Sites\SiteContract
     */
    public function create()
    {
        $site = $this->belongsSite($this->url->domain());

        $siteClass = $this->siteClasses[$site] ?? null;

        if (!class_exists($siteClass)) {
            return new NotRegisted($this->url);
        }

        return new $siteClass($this->url);
    }

    /**
     * use url's domain to check is it exists in domains look up table
     *
     * if do, return belongs site
     * else return its own domain
     *
     * @param stirng $domain
     *
     * @return string
     */
    protected function belongsSite($domain)
    {
        foreach ($this->domainsTable as $site => $domains) {
            if (in_array($domain, $domains)) {
                return $site;
            }
        }

        return $domain;
    }
}
