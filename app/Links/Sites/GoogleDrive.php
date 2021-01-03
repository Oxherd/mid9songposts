<?php

namespace App\Links\Sites;

use Illuminate\Support\Str;

class GoogleDrive extends SiteContract
{
    public function name()
    {
        return 'google_drive';
    }

    public function getResourceId()
    {
        return Str::between($this->url->path(), '/d/', '/') ?: null;
    }

    public static function generalUrl($resource_id)
    {
        return "https://drive.google.com/file/d/{$resource_id}/view";
    }
}
