<?php

namespace App\Posts\Baha;

use App\Exceptions\NotExpectedPageException;
use App\Links\UrlString;
use App\Models\Thread;
use App\Posts\Baha\PostSection;
use Carbon\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;
use InvalidArgumentException;
use Symfony\Component\DomCrawler\Crawler;

class ThreadPage
{
    /**
     * @property Crawler use for html interaction
     */
    protected $html;

    /**
     * @property Thread
     */
    protected $thread;

    /**
     * @property UrlString delegate UrlString to get url params
     */
    protected $url;

    /**
     * @param string|mixed $url
     */
    public function __construct($url)
    {
        $this->url = $url instanceof UrlString ? $url : new UrlString($url);

        $this->ensureIsExpectedUrl();

        $this->html = new Crawler((string) Http::get($url));
    }

    /**
     * persist thread and following posts in current page into database
     *
     * @return void
     */
    public function handle()
    {
        if (!$this->thread) {
            $this->save();
        }

        $this->posts()->each(function (PostSection $section) {
            $section->save($this->thread->id);
        });
    }

    /**
     * save all thread's related data into database
     * or retrieve a existed row from database if there has one
     *
     * assign to $this->thread property, avoid additional query when calling
     *
     * @return Thread
     */
    public function save()
    {
        return $this->thread ??
        $this->thread = Thread::firstOrCreate([
            'no' => $this->index(),
        ], [
            'title' => $this->title(),
            'date' => $this->date(),
        ]);
    }

    /**
     * every thread has its own number
     *
     * extract that no from meta-data, so in single post thread it can also work
     *
     * @return string
     */
    public function index()
    {
        try {
            $url = $this->html
                ->filter('meta[property="al:ios:url"]')
                ->first()
                ->attr('content');
        } catch (InvalidArgumentException $e) {
            throw new NotExpectedPageException('This thread or post no longer available.');
        }

        return (new UrlString($url))->query('snA');
    }

    /**
     * get thread's title from scraped html
     *
     * sometime it will get string like "RE:【情報】.."
     * add trimmer get rid of the "RE:" thingy
     *
     * @return string
     *
     * @throws InvalidArgumentException if somehow can't get title node content string
     */
    public function title()
    {
        return Str::between($this->html->filter('title')->text(), ':', ' @');
    }

    /**
     * get thread's published date
     *
     * extract month and day from title, something like "12/1 半夜..."
     * the year is according to first post's published date from the same page
     *
     * if fetched thread's date compare to first post date is far too long
     * (like almost a year)
     * it probably means the post was published from different year
     * then shrink down thread's published date one year
     *
     * @return string formated: Y-m-d
     *
     * @throws InvalidArgumentException if somehow can't get header info node from first post
     */
    public function date()
    {
        $firstPostDate = $this->getDateFromFirstPost();

        $expectedDate = $this->getDateFromTitle($firstPostDate);

        if ($expectedDate->diffInDays($firstPostDate) >= 300) {
            $expectedDate->subYear();
        }

        return $expectedDate->toDateString();
    }

    /**
     * get all posts from this thread page
     *
     * @return Collection[PostSection]
     */
    public function posts()
    {
        $posts = $this->html->filter('.c-section[id^="post_"]');

        if (!$posts->count()) {
            throw new NotExpectedPageException('Has the html structure or CSS changed?');
        }

        return Collection::make(
            $posts->each(function (Crawler $post) {
                return new PostSection($post);
            })
        );
    }

    /**
     * generate a new instance with next page url
     *
     * @return ThreadPage|null return null if html indicate there is no more page
     */
    public function nextPage()
    {
        if ($this->hasNextPage()) {
            return new self($this->url->nextPage());
        }

        return null;
    }

    /**
     * delegate url property getter
     *
     * @return UrlString
     */
    public function url()
    {
        return $this->url;
    }

    /**
     * ensure url/page is expected target in order to fetch data correctly
     *
     * @throws NotExpectedPageException
     */
    protected function ensureIsExpectedUrl()
    {
        if (
            $this->url()->domain() === 'forum.gamer.com.tw' &&
            ($this->url()->path() === '/C.php' || $this->url()->path() === '/Co.php') &&
            ($this->url()->hasQuery('snA') || $this->url()->hasQuery('sn'))
        ) {
            return;
        }

        throw new NotExpectedPageException();
    }

    /**
     * check is there has more page can go or not
     *
     * @return bool
     */
    public function hasNextPage()
    {
        try {
            return !!$this->html->filter('.pagenow')->first()->nextAll()->text('');
        } catch (InvalidArgumentException $e) {
            return false;
        }
    }

    /**
     * get first post's published date from current thread html
     *
     * @return \Carbon\Carbon
     */
    protected function getDateFromFirstPost()
    {
        $dateTime = $this->html
            ->filter('.c-post__header__info a[data-mtime]')
            ->first()
            ->attr('data-mtime');

        return Carbon::createFromFormat('Y-m-d H:i:s', $dateTime);
    }

    /**
     * generate expected thread's published date from title
     *
     * if title did not has any valid month/day, use first post published date instead
     *
     * @return \Carbon\Carbon
     */
    protected function getDateFromTitle(Carbon $firstPostDate)
    {
        preg_match('/\d{1,2}\/\d{1,2}/', $this->title(), $monthDay);

        $monthDay = empty($monthDay) ? $firstPostDate->format('m/d') : $monthDay[0];

        return Carbon::createFromFormat('Y/m/d', "{$firstPostDate->year}/$monthDay");
    }
}
