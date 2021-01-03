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
        return Str::afterLast($this->url->path(), '/') ?: null;
    }

    public static function generalUrl($resource_id)
    {
        return "https://vlog.xuite.net/play/{$resource_id}";
    }
}
