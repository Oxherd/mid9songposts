<?php

namespace App\Links\Sites;

use Illuminate\Support\Str;

class Youtube extends SiteContract
{
    public function name()
    {
        return 'youtube';
    }

    public function getResourceId()
    {
        if ($this->isNotVideo()) {
            return null;
        }

        return $this->walkCondition() ?: null;
    }

    public static function generalUrl($resource_id)
    {
        return "https://www.youtube.com/watch?v={$resource_id}";
    }

    public static function embeddedUrl($resource_id)
    {
        return "https://www.youtube.com/embed/{$resource_id}";
    }

    /**
     * check given url is provide video or not
     *
     * @return bool
     */
    protected function isNotVideo()
    {
        return
            !$this->url->hasQuery('v') &&
            !$this->isShortUrl() &&
            !$this->isEmbedUrl();
    }

    /**
     * check given url is short url version or not
     *
     * @return bool
     */
    protected function isShortUrl()
    {
        return $this->url->domain() === 'youtu.be';
    }

    /**
     * check given url is embeded video url
     *
     * @return bool
     */
    protected function isEmbedUrl()
    {
        return Str::startsWith($this->url->path(), '/embed');
    }

    /**
     * check all conditions to determine which result available
     *
     * @return string
     */
    protected function walkCondition()
    {
        if ($this->isShortUrl()) {
            return trim($this->url->path(), '/');
        }

        if ($this->isEmbedUrl()) {
            return str_replace('/embed/', '', $this->url->path());
        }

        if ($this->url->hasQuery('v')) {
            return $this->url->query('v');
        }
    }
}
