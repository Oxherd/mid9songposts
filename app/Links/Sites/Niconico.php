<?php

namespace App\Links\Sites;

use Illuminate\Support\Str;

class Niconico extends SiteContract
{
    public function name()
    {
        return 'niconico';
    }

    public function getResourceId()
    {
        return Str::after($this->url->path(), '/watch/') ?: null;
    }

    public static function generalUrl($resource_id)
    {
        return "https://www.nicovideo.jp/watch/{$resource_id}";
    }

    public static function embeddedUrl($resource_id)
    {
        return "https://embed.nicovideo.jp/watch/{$resource_id}";
    }
}
