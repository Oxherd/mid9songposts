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
}
