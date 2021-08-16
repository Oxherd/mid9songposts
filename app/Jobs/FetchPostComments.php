<?php

namespace App\Jobs;

use App\Models\Comment;
use App\Models\Post;
use App\Posts\Baha\PosterData;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Http;

class FetchPostComments implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $post;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct(Post $post)
    {
        $this->post = $post;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $url = "https://forum.gamer.com.tw/ajax/moreCommend.php?bsn=60076&snB=";

        $response = Http::get("{$url}{$this->post->no}");

        if (!$response->successful()) {
            $this->fail();
        }

        collect($response->json())->except('next_snC')->each(function($data) {
            Comment::create([
                'post_id' => $this->post->id,
                'poster_id' => (new PosterData($data['userid'], $data['nick']))->save()->id,
                'content' => $data['content'],
                'created_at' => '2021-07-22 17:35:55',
                'inserted_at' => now()->toDateTimeString(),
            ]);
        });
    }
}
