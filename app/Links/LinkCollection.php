<?php

namespace App\Links;

use App\Models\InvalidLink;
use App\Models\Link;
use Illuminate\Support\Collection;
use PharIo\Manifest\InvalidUrlException;

class LinkCollection
{
    /**
     * @property \Illuminate\Support\Collection
     */
    protected $links;

    /**
     *
     * @property string
     */
    protected static $pattern = "/(https?:\/\/)?[-a-zA-Z0-9@:%._\+~#=]{1,256}\.[a-zA-Z0-9()]{1,6}\b([-a-zA-Z0-9()@:%_\+.~#?&\/\/=]*)/";

    /**
     * @param \Illuminate\Support\Collection $links
     */
    protected function __construct(Collection $links)
    {
        $this->links = $this->convertToLinkModel($links);
    }

    /**
     * extract all url from target string text
     *
     * @param string $text
     *
     * @return \App\Links\LinkCollection
     */
    public static function fromText(string $text)
    {
        preg_match_all(self::$pattern, $text, $matches);

        return new static(Collection::make($matches[0]));
    }

    /**
     * a getter for $links property
     *
     * @return \Illuminate\Support\Collection
     */
    public function get()
    {
        return $this->links;
    }

    /**
     * get rid of duplicated url according to Link's 'site' and 'resource' attributes
     *
     * if resource attribute is empty, merge same site url together
     * even the path might be different
     *
     * for example: 'http://foo.bar/baz' is treat as same as 'http://foo.bar/qux'
     * just because the domain 'foo.bar' not in domains lookup table, and it can't get resource id
     *
     * @return \App\Links\LinkCollection
     */
    public function unique()
    {
        $this->links = $this->links
            ->unique(function ($link) {
                return $link['site'] . $link['resource_id'];
            });

        return $this;
    }

    /**
     * filter down link that 'site' attribute not in lookup table
     * because it can not retrieve resource id (which mean 'resource' attribute is empty)
     *
     * @return \App\Links\LinkCollection
     */
    public function filter()
    {
        $this->links = $this->links->filter(function ($link) {
            return $link['resource_id'];
        });

        return $this;
    }

    /**
     * prepare all link url for further usage by initiate a Link instance
     *
     * @return \Illuminate\Support\Collection
     */
    protected function convertToLinkModel(Collection $links)
    {
        return $links->map(function ($link) {
            try {
                return Link::make([
                    'original' => $link,
                ]);
            } catch (InvalidUrlException $e) {
                InvalidLink::create(['url' => $link]);

                return new Link();
            }
        });
    }
}
