<?php

namespace App\Jobs;

use App\Models\Post;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class CleanUpPostsWithoutLink implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        Post::select('posts.*')
            ->leftJoin('links', 'links.post_id', '=', 'posts.id')
            ->leftJoin('no_music', function ($join) {
                $join->on('no_music.morph_id', '=', 'posts.id')
                    ->where('morph_type', Post::class);
            })
            ->whereNull('links.id')
            ->whereNull('no_music.id')
            ->get()
            ->each(function (Post $post) {
                $post->extractLinks();

                if (!$post->links()->count()) {
                    $post->noMusic()->create();
                }
            });
    }
}
