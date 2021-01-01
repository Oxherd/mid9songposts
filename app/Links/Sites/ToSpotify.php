<?php

namespace App\Links\Sites;

use App\Links\LinkCollection;
use Illuminate\Support\Facades\Http;

class ToSpotify extends SiteContract
{
    public function name()
    {
        return 'spotify';
    }

    /**
     * this site will redirect user to real spotify page
     *
     * user go into redirect page first
     * then the page's javascript doing redirect thingy
     *
     * needs extract expected spotify url from that redirect page
     *
     * @return string|null
     */
    public function getResourceId()
    {
        $redirectPage = Http::get((string) $this->url);

        $extractedUrl = LinkCollection::fromText((string) $redirectPage)
            ->unique()
            ->filter()
            ->get()
            ->first()
            ->original;

        return (new Spotify($extractedUrl))->getResourceId();
    }
}
