<?php

namespace App\Jobs;

use App\Models\Link;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Http;
use Symfony\Component\DomCrawler\Crawler;

class ScrapeLinkTitle implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * use Link model to retrieve url
     *
     * @property Link
     */
    public $link;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct(Link $link)
    {
        $this->link = $link;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $response = Http::get($this->link->general());

        if ($response->successful()) {
            $html = new Crawler((string) $response);

            $this->link->update([
                'title' => $html->filter('title')->text(),
            ]);
        }

        $response->close();
    }
}
