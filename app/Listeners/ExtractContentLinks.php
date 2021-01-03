<?php

namespace App\Listeners;

use App\Links\LinkCollection;
use App\Models\Link;
use App\Models\Post;
use Illuminate\Support\Str;
use Symfony\Component\DomCrawler\Crawler;

class ExtractContentLinks
{
    /**
     * Handle the event.
     *
     * @param  \App\Events\PostCreated\PostCreated  $event
     * @return void
     */
    public function handle($event)
    {
        $event->post->extractLinks();
    }

    /**
     * sometimes poster will quote other people's post
     *
     * in order to avoid count duplicate link as same poster
     * filter down quoted content
     *
     * quoted content may look like this: "> this is quoted content"
     *
     * @param string $text
     *
     * @return string
     */
    protected function filterQuotedContent($text)
    {
        $html = new Crawler($text);

        $html = $html->filter('body > *')
            ->each(function (Crawler $node) {
                return Str::startsWith($node->text(), '>') ?
                null :
                $node->outerHtml();
            });

        return implode($html);
    }
}
