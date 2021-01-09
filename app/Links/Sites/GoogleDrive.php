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
        return $this->walkCondition() ?: null;
    }

    public static function generalUrl($resource_id)
    {
        return "https://drive.google.com/file/d/{$resource_id}/view";
    }

    /**
     * determine what segment of resource id should return
     *
     * @return string
     */
    protected function walkCondition()
    {
        if ($this->isOpenRedirect()) {
            return $this->url->query('id');
        }

        return Str::between($this->url->path(), '/d/', '/');
    }

    /**
     * check url string is a redirect
     *
     * @return bool
     */
    protected function isOpenRedirect()
    {
        return $this->url->path() === '/open';
    }
}
