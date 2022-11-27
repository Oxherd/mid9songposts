<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Symfony\Component\Panther\Client;
use Symfony\Component\Panther\DomCrawler\Crawler;

class CheckBahaPageLayout extends Command
{
    protected const PASSED = 'Passed';

    protected const WARN = 'Warn';

    protected const FAILED = 'Failed';

    protected $threadPageUrl = '';

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'check-baha';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = "Check Baha page's layout(html/css) changed or not";

    protected $reports = [
        'Search Title Page Items' => self::PASSED,
        'Search Title Page Titles' => self::PASSED,
        'Search Title Page Users' => self::PASSED,
        'Thread Page Url' => self::PASSED,
        'Thread Page Title' => self::PASSED,
        'Thread Page Posts' => self::PASSED,
        'Thread Page Created At' => self::PASSED,
        'Post Section Index' => self::PASSED,
        'Post Section Content' => self::PASSED,
        'Post Section Created At' => self::PASSED,
        'Post Section User Id' => self::PASSED,
        'Post Section User Name' => self::PASSED,
        'Search User Page Items' => self::PASSED,
        'Paginator' => self::PASSED,
    ];

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        /** @var \Symfony\Component\Panther\Client */
        $client = app(Client::class);

        $this->info('Checking search title page...');

        $this->checkSearchTitlePage($client);

        $this->info('Search title page checked.');

        $this->info('Checking thread page with: ' . $this->threadPageUrl);

        $firstPost = $this->checkThreadPage($client);

        $this->info('Thread page checked.');

        $this->info('Checking Post Section...');

        $this->checkPostSection($firstPost);

        $this->info('Post section checked.');

        $this->info('Checking search user page...');

        $this->checkSearchUserPage($client);

        $this->info('Search user page checked.');

        $this->outputResults();
    }

    protected function checkSearchTitlePage(Client $client)
    {
        $page = $client->request('GET', 'https://forum.gamer.com.tw/B.php?bsn=60076&qt=1&q=半夜歌串一人一首');

        $items = $page->filter('.b-list-item');

        $this->setCountableSearchReport('Search Title Items', $items->count());

        $titles = $page->filter('.b-list__main__title');

        $this->setCountableSearchReport('Search Title Item Titles', $titles->count());

        $path = $titles->first()->attr('href');

        $this->threadPageUrl = "https://forum.gamer.com.tw/{$path}";

        $users = $page->filter('.b-list__count__user');

        $this->setCountableSearchReport('Search Title Users', $users->count());

        $this->checkPaginator($page);
    }

    protected function checkThreadPage(Client $client)
    {
        $page = $client->request('GET', $this->threadPageUrl);

        $content = $page->filter('meta[property="al:ios:url"]')->attr('content');

        if (!$content) $this->reports['Thread Page Url'] = self::FAILED;

        $title = $client->getTitle();

        if (!$title) $this->reports['Thread Page Title'] = self::FAILED;

        $posts = $page->filter('.c-section[id^="post_"]');

        if (!$posts->count()) $this->reports['Thread Page Posts'] = self::FAILED;

        $createdAt = $page->filter('.c-post__header__info a[data-mtime]')->first()->attr('data-mtime');

        if (!$createdAt) $this->reports['Thread Page Created At'] = self::FAILED;

        $this->checkPaginator($page);

        return $posts->first();
    }

    protected function checkPostSection(Crawler $post)
    {
        $id = $post->filter('.c-article')->attr('id');

        if (!$id) $this->reports['Post Section Index'] = self::FAILED;

        /** @var \Facebook\WebDriver\Remote\RemoteWebElement */
        $webElement = $post->filter('.c-article__content')->getElement(0);

        $content = $webElement->getDomProperty('innerHTML');

        if (!$content) $this->reports['Post Section Content'] = self::FAILED;

        $createdAt = $post->filter('a[data-mtime]')->attr('data-mtime');

        if (!$createdAt) $this->reports['Post Section Created At'] = self::FAILED;

        $userid = $post->filter('.userid')->text();

        if (!$userid) $this->reports['Post Section User Id'] = self::FAILED;

        $username = $post->filter('.username')->text();

        if (!$username) $this->reports['Post Section User Name'] = self::FAILED;
    }

    protected function checkSearchUserPage(Client $client)
    {
        $page = $client->request('GET', 'https://forum.gamer.com.tw/Bo.php?bsn=60076&qt=6&q=a7752876');

        $items = $page->filter('.b-list__main > a');

        $this->setCountableSearchReport('Search User Page Items', $items->count());

        $this->checkPaginator($page);
    }

    protected function outputResults()
    {
        $this->table(['Subject', 'Report'], collect($this->reports)->map(function ($value, $key) {
            $color = $value == self::PASSED ? 'green' : ($value == self::WARN ? 'yellow' : 'red');

            return [
                $key,
                "<fg={$color}>{$value}</>",
            ];
        }));
    }

    protected function setCountableSearchReport($key, $count)
    {
        if ($count === 0) {
            $this->reports[$key] = self::FAILED;
        } else if ($count < 30) {
            $this->reports[$key] = self::WARN;
        }
    }

    protected function checkPaginator(Crawler $page)
    {
        $pagenow = $page->filter('.pagenow');

        $this->reports['Paginator'] = $pagenow->count() ? $this->reports['Paginator'] : self::FAILED;
    }
}
