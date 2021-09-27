<?php

namespace App\Jobs;

use App\Models\Post;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Queue;

class FetchCommentsFromExistedPosts implements ShouldQueue
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
     * Fetch comments from existed posts by manual execute
     *
     * @return void
     */
    public function handle()
    {
        Post::select('posts.*')
            ->leftJoin('comments', 'posts.id', '=', 'comments.post_id')
            ->whereNull('comments.id')
            ->chunk(50, function ($posts) {
                foreach ($posts as $post) {
                    $jobs[] = new FetchPostComments($post);
                }

                Queue::bulk($jobs, '', 'scrape');
            });
    }
}
