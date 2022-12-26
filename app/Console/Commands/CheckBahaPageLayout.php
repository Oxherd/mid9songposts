<?php

namespace App\Console\Commands;

use GuzzleHttp\Client;
use Illuminate\Console\Command;
use Symfony\Component\DomCrawler\Crawler;
use Symfony\Component\Panther\Client as PantherClient;
use Symfony\Component\Panther\DomCrawler\Crawler as PantherCrawler;

class CheckBahaPageLayout extends Command
{
    protected const PASSED = 'Passed';

    protected const WARN = 'Warn';

    protected const FAILED = 'Failed';

    protected const GUZZLE = 'guzzle';

    protected const PANTHER = 'panther';

    protected $threadPageUrl = '';

    protected Client|PantherClient $cachedClient;

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
        'Search Title Page Items' => [self::GUZZLE => self::PASSED, self::PANTHER => self::PASSED],
        'Search Title Page Titles' => [self::GUZZLE => self::PASSED, self::PANTHER => self::PASSED],
        'Search Title Page Users' => [self::GUZZLE => self::PASSED, self::PANTHER => self::PASSED],
        'Thread Page Url' => [self::GUZZLE => self::PASSED, self::PANTHER => self::PASSED],
        'Thread Page Title' => [self::GUZZLE => self::PASSED, self::PANTHER => self::PASSED],
        'Thread Page Posts' => [self::GUZZLE => self::PASSED, self::PANTHER => self::PASSED],
        'Thread Page Created At' => [self::GUZZLE => self::PASSED, self::PANTHER => self::PASSED],
        'Post Section Index' => [self::GUZZLE => self::PASSED, self::PANTHER => self::PASSED],
        'Post Section Content' => [self::GUZZLE => self::PASSED, self::PANTHER => self::PASSED],
        'Post Section Created At' => [self::GUZZLE => self::PASSED, self::PANTHER => self::PASSED],
        'Post Section User Id' => [self::GUZZLE => self::PASSED, self::PANTHER => self::PASSED],
        'Post Section User Name' => [self::GUZZLE => self::PASSED, self::PANTHER => self::PASSED],
        'Search User Page Items' => [self::GUZZLE => self::PASSED, self::PANTHER => self::PASSED],
        'Paginator' => [self::GUZZLE => self::PASSED, self::PANTHER => self::PASSED],
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
        $this->checkBahaPageLayoutBy(self::GUZZLE);
        $this->checkBahaPageLayoutBy(self::PANTHER);

        $this->outputResults();
    }

    protected function checkBahaPageLayoutBy($by)
    {
        config(['app.scrape_by' => $by]);

        $this->cachedClient = app(Client::class);

        $this->info("Checking search title page by {$by}...");

        $this->checkSearchTitlePage();

        $this->info("Search title page by {$by} checked.");

        $this->info("Checking thread page by {$by} with: {$this->threadPageUrl}");

        $firstPost = $this->checkThreadPage();

        $this->info("Thread page by {$by} checked.");

        $this->info("Checking Post Section by {$by}...");

        $this->checkPostSection($firstPost);

        $this->info("Post section by {$by} checked.");

        $this->info("Checking search user page by {$by}...");

        $this->checkSearchUserPage();

        $this->info("Search user page by {$by} checked.");
    }

    protected function checkSearchTitlePage()
    {
        $page = $this->getPage('https://forum.gamer.com.tw/B.php?bsn=60076&qt=1&q=半夜歌串一人一首');

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

    protected function checkThreadPage()
    {
        $page = $this->getPage($this->threadPageUrl);

        $content = $page->filter('meta[property="al:ios:url"]')->attr('content');

        if (!$content) $this->reports['Thread Page Url'][config('app.scrape_by')] = self::FAILED;

        if (config('app.scrape_by') === self::GUZZLE) {
            $title = $page->filter('title')->text();
        } else {
            $title = $this->cachedClient->getTitle();
        }

        $this->info("Thread Page Title is {$title}");

        if (!$title) $this->reports['Thread Page Title'][config('app.scrape_by')] = self::FAILED;

        $posts = $page->filter('.c-section[id^="post_"]');

        if (!$posts->count()) $this->reports['Thread Page Posts'][config('app.scrape_by')] = self::FAILED;

        $createdAt = $page->filter('.c-post__header__info a[data-mtime]')->first()->attr('data-mtime');

        if (!$createdAt) $this->reports['Thread Page Created At'][config('app.scrape_by')] = self::FAILED;

        $this->checkPaginator($page);

        return $posts->first();
    }

    protected function checkPostSection(Crawler|PantherCrawler $post)
    {
        $id = $post->filter('.c-article')->attr('id');

        if (!$id) $this->reports['Post Section Index'][config('app.scrape_by')] = self::FAILED;

        if (config('app.scrape_by') === self::GUZZLE) {
            $content = $post->filter('.c-article__content')->html();
        } else {
            /** @var \Facebook\WebDriver\Remote\RemoteWebElement */
            $webElement = $post->filter('.c-article__content')->getElement(0);

            $content = $webElement->getDomProperty('innerHTML');
        }

        if (!$content) $this->reports['Post Section Content'][config('app.scrape_by')] = self::FAILED;

        $createdAt = $post->filter('a[data-mtime]')->attr('data-mtime');

        if (!$createdAt) $this->reports['Post Section Created At'][config('app.scrape_by')] = self::FAILED;

        $userid = $post->filter('.userid')->text();

        if (!$userid) $this->reports['Post Section User Id'][config('app.scrape_by')] = self::FAILED;

        $username = $post->filter('.username')->text();

        if (!$username) $this->reports['Post Section User Name'][config('app.scrape_by')] = self::FAILED;
    }

    protected function checkSearchUserPage()
    {
        $page = $this->getPage('https://forum.gamer.com.tw/Bo.php?bsn=60076&qt=6&q=a7752876');

        $items = $page->filter('.b-list__main > a');

        $this->setCountableSearchReport('Search User Page Items', $items->count());

        $this->checkPaginator($page);
    }

    protected function outputResults()
    {
        $this->table(['Subject', 'Guzzle', 'Panther'], collect($this->reports)->map(function ($results, $key) {
            [self::GUZZLE => $guzzleResult, self::PANTHER => $pantherResult] = $results;

            $guzzleColor = $guzzleResult == self::PASSED ? 'green' : ($guzzleResult == self::WARN ? 'yellow' : 'red');
            $pantherColor = $pantherResult == self::PASSED ? 'green' : ($pantherResult == self::WARN ? 'yellow' : 'red');

            return [
                $key,
                "<fg={$guzzleColor}>{$guzzleResult}</>",
                "<fg={$pantherColor}>{$pantherResult}</>",
            ];
        }));
    }

    protected function getPage(string $url): Crawler|PantherCrawler
    {
        $response = $this->cachedClient->request('GET', $url);

        if (config('app.scrape_by') == self::GUZZLE) {
            return new Crawler((string) $response->getBody());
        } else {
            return $response;
        }
    }

    protected function setCountableSearchReport(string $key, int $count)
    {
        if ($count === 0) {
            $this->reports[$key][config('app.scrape_by')] = self::FAILED;
        } else if ($count < 30) {
            $this->reports[$key][config('app.scrape_by')] = self::WARN;
        }
    }

    protected function checkPaginator(Crawler $page)
    {
        $pagenow = $page->filter('.pagenow');

        $this->reports['Paginator'][config('app.scrape_by')] = $pagenow->count()
            ? $this->reports['Paginator'][config('app.scrape_by')]
            : self::FAILED;
    }
}
