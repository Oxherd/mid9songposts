<?php

namespace App\Links\Sites;

class NotRegisted extends SiteContract
{
    /**
     * there is not correspond site, so return its own domain
     *
     * @return string
     */
    public function name()
    {
        return $this->url->domain();
    }

    /**
     * there is no expected resource id can retreive
     *
     * @return null
     */
    public function getResourceId()
    {
        return null;
    }

    /**
     * won't use this method in general
     */
    public static function generalUrl($resource_id)
    {
    }

    /**
     * won't use this method in embedded
     */
    public static function embeddedUrl($resource_id)
    {
    }
}
