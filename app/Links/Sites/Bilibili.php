<?php

namespace App\Links\Sites;

use Illuminate\Support\Str;

class Bilibili extends SiteContract
{
    public function name()
    {
        return 'bilibili';
    }

    public function getResourceId()
    {
        return $this->walkCondition() ?: null;
    }

    public static function generalUrl($resource_id)
    {
        return "https://www.bilibili.com/video/{$resource_id}";
    }

    /**
     * check given url is provide general video or not
     *
     * @return bool
     */
    protected function isGeneralUrl()
    {
        return Str::contains($this->url->path(), '/video/');
    }

    /**
     * check given url is embeded video url
     *
     * @return bool
     */
    protected function isEmbedUrl()
    {
        return Str::contains((string) $this->url, 'player.bilibili.com/player');
    }

    protected function walkCondition()
    {
        if ($this->isGeneralUrl()) {
            return Str::after($this->url->path(), '/video/');
        }

        if ($this->isEmbedUrl()) {
            return $this->url->query('bvid');
        }
    }
}
