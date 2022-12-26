<?php

namespace App\Links\Sites;

use Illuminate\Support\Str;

class Xuite extends SiteContract
{
    public function name()
    {
        return 'xuite';
    }

    public function getResourceId()
    {
        return trim($this->walkCondition(), '/') ?: null;
    }

    public static function generalUrl($resource_id)
    {
        return "https://vlog.xuite.net/play/{$resource_id}";
    }

    public static function embeddedUrl($resource_id)
    {
        return "https://vlog.xuite.net/embed/{$resource_id}";
    }

    /**
     * determine how resource id segment return
     *
     * @return string
     */
    protected function walkCondition()
    {
        if ($this->isEmbedUrl()) {
            return Str::after($this->url->path(), '/embed');
        }

        if ($this->isMobileUrl()) {
            $paths = explode('/', $this->url->path());

            return $paths[3] ?? '';
        }

        return Str::afterLast($this->url->path(), '/play');
    }

    /**
     * check is url a embed string
     *
     * @return bool
     */
    protected function isEmbedUrl()
    {
        return Str::startsWith($this->url->path(), '/embed');
    }

    /**
     * check is url a mobile string
     *
     * @return bool
     */
    protected function isMobileUrl()
    {
        return $this->url->domain() === 'm.xuite.net';
    }
}
