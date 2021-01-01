<?php

namespace App\Links\Sites;

use Illuminate\Support\Str;

class Spotify extends SiteContract
{
    public function name()
    {
        return 'spotify';
    }

    public function getResourceId()
    {
        return $this->walkCondition() ?: null;
    }

    /**
     * check each condition is passable and return extracted result
     *
     * because spotify use same domain with different path for different bussiness
     * needs check url specifically
     *
     * @return stirng|void
     */
    protected function walkCondition()
    {
        if ($this->isTrackUrl()) {
            return Str::afterLast($this->url->path(), '/track/');
        }

        if ($this->isEmbedUrl()) {
            return Str::afterLast($this->url->path(), '/embed/track/');
        }
    }

    protected function isTrackUrl()
    {
        return Str::startsWith($this->url->path(), '/track/');
    }

    protected function isEmbedUrl()
    {
        return Str::startsWith($this->url->path(), '/embed/track/');
    }
}
