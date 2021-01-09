<?php

namespace App\Jobs;

use App\Models\Link;
use Google_Client;
use Google_Service_Sheets;
use Google_Service_Sheets_ValueRange;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class PushLinksToGoogleSheet implements ShouldQueue
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
     * @param \Google_Client $client inject Google_Client instance for eazy test
     *
     * @return void
     */
    public function handle(Google_Client $client)
    {
        $this->setupGoogleClient($client);

        $service = new Google_Service_Sheets($client);

        $maxId = $this->getMaximumId($service);

        $this->getLinkBuilder($maxId)
            ->chunk(500, function ($links) use ($service) {
                $body = new Google_Service_Sheets_ValueRange([
                    'values' => $this->setupSheetValues($links),
                ]);

                $optParams = [
                    'valueInputOption' => 'RAW',
                ];

                $service->spreadsheets_values->append(
                    config('services.google.sheet_id'),
                    '歌曲',
                    $body,
                    $optParams
                );
            });
    }

    /**
     * setup google client
     *
     * @param \Google_Client $client
     *
     * @return void
     */
    protected function setupGoogleClient(Google_Client $client)
    {
        $client->useApplicationDefaultCredentials();
        $client->setApplicationName('半夜歌串一人一首');
        $client->setScopes(Google_Service_Sheets::SPREADSHEETS);
    }

    /**
     * retreive the maximum id from target google sheet
     *
     * @param \Google_Service_Sheets $service
     *
     * @return int|null
     */
    protected function getMaximumId(Google_Service_Sheets $service)
    {
        $values = $service->spreadsheets_values->get(
            config('services.google.sheet_id'),
            'A2:A', [
                'majorDimension' => 'COLUMNS',
            ])
            ->values;

        return empty($values) ? 0 : (int) max($values[0]);
    }

    /**
     * build up a Link's query builder for further usage
     *
     * @param int $maxId query id bigger than this number
     *
     * @return \Illuminate\Database\Eloquent\Builder\Builder
     */
    protected function getLinkBuilder($maxId = 0)
    {
        return Link::select([
            'links.id as id',
            'posters.account as account',
            'links.site as site',
            'links.resource_id as resource_id',
            'posts.created_at as published_at',
            'threads.title as title',
            'posts.no as post_no',
        ])
            ->leftJoin('posts', 'posts.id', '=', 'links.post_id')
            ->leftJoin('posters', 'posts.poster_id', '=', 'posters.id')
            ->leftJoin('threads', 'threads.id', '=', 'posts.thread_id')
            ->where('links.id', '>', $maxId)
            ->orderBy('id');
    }

    /**
     * setup data set that uplaod to google sheet
     *
     * @param \Illuminate\Database\Eloquent\Collection $links
     *
     * @return array
     */
    protected function setupSheetValues(Collection $links)
    {
        foreach ($links as $link) {
            $values[] = [
                $link->id,
                $link->account,
                $link->general(),
                $link->published_at,
                $link->title,
                "https://forum.gamer.com.tw/Co.php?bsn=60076&sn={$link->post_no}",
                $link->site,
                $link->resource_id,
            ];
        }

        return $values;
    }
}
