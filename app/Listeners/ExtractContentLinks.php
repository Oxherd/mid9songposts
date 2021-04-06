<?php

namespace App\Listeners;

use App\Models\Post;

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
}
