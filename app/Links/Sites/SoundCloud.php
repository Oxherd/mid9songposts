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
        $soundsId = $this->getSoundsId();

        if (!$soundsId) {
            return null;
        }

        return "{$this->url->path()}?sounds={$soundsId}";
    }

    /**
     * retreive SoundCloud's music id (soundcloud://sounds:xxxxx)
     *
     * if can not retreivce, it mean given url page is not general music page
     *
     * @return string|null
     */
    protected function getSoundsId()
    {
        $SCpage = Http::get((string) $this->url);

        $html = new Crawler((string) $SCpage);

        $metadata = $html->filter('meta[property="al:android:url"]')->attr('content');

        return
        Str::contains($metadata, '//sounds') ?
        Str::after($metadata, '//sounds:') :
        null;
    }
}
