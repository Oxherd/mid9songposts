<?php

namespace App\Links\Sites;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;
use Symfony\Component\DomCrawler\Crawler;

class SoundCloud extends SiteContract
{
    public function name()
    {
        return 'sound_cloud';
    }

    public function getResourceId()
    {
        $SCpage = Http::get((string) $this->url);

        $html = new Crawler((string) $SCpage);

        $soundsId = $this->extractSoundsId($html);

        if (!$soundsId) {
            return null;
        }

        $path = Str::after(
            $html->filter('link[rel="canonical"]')->attr('href'),
            'soundcloud.com'
        );

        return "{$path}?tracks={$soundsId}";
    }

    public static function generalUrl($resource_id)
    {
        $resource_id = trim($resource_id, '/');

        return "https://soundcloud.com/{$resource_id}";
    }

    public static function embeddedUrl($resource_id)
    {
        $resource_id = Str::after($resource_id, '?tracks=');

        return "https://w.soundcloud.com/player/?url=https%3A//api.soundcloud.com/tracks/{$resource_id}";
    }

    /**
     * extract SoundCloud's music id (soundcloud://sounds:xxxxx) from scraped html
     *
     * if extract nothing, it mean given html is not general music page
     * which will return null
     *
     * @param Crawler $html
     *
     * @return string|null
     */
    protected function extractSoundsId($html)
    {
        $metadata = $html->filter('meta[property="al:android:url"]')->attr('content');

        return
            Str::contains($metadata, '//sounds') ?
            Str::after($metadata, '//sounds:') :
            null;
    }
}
