<?php

namespace App\Posts\Baha;

use App\Models\Post;
use App\Models\Poster;
use App\Models\Thread;
use Illuminate\Support\Str;
use InvalidArgumentException;
use Symfony\Component\DomCrawler\Crawler;

class PostSection
{
    /**
     * @property Crawler for html interact
     */
    protected $html;

    /**
     * @property Post
     */
    protected $post;

    /**
     * @param string|mixed $html
     */
    public function __construct($html)
    {
        $this->html = $html instanceof Crawler ? $html : new Crawler($html);
    }

    /**
     * save post's related data into database
     * or retrieve a exists row from it
     * and it won't create same post twice
     *
     * @param int $thread_id
     * pass this param for relationship connection
     * but its nullable in database scheme
     *
     * @return Post
     */
    public function save($thread_id = null)
    {
        return $this->post ??
        $this->post = Post::firstOrCreate(
            [
                'poster_id' => $this->poster()->save()->id,
                'thread_id' => $thread_id,
                'no' => $this->index(),
            ],
            [
                'content' => $this->content(),
                'created_at' => $this->createdAt(),
                'inserted_at' => now()->toDateTimeString(),
            ]
        );
    }

    /**
     * get post id from outer html attribute
     *
     * add trimmer get rid of 'post_', only needs the number after it
     *
     * @return string
     *
     * @throws InvalidArgumentException if somehow can't get expected html
     */
    public function index()
    {
        return Str::after($this->html->filter('.c-section')->attr('id'), 'post_');
    }

    /**
     * get post content's raw html
     *
     * @return string
     */
    public function content()
    {
        return urldecode($this->html->filter('.c-article__content')->html());
    }

    /**
     * get post published time
     *
     * @return string
     */
    public function createdAt()
    {
        return $this->html->filter('a[data-mtime]')->attr('data-mtime');
    }

    /**
     * extract poster data into its own instance
     *
     * @return PosterData
     */
    public function poster()
    {
        return new PosterData(
            $account = $this->html->filter('.userid')->text(),
            $name = $this->html->filter('.username')->text(),
        );
    }
}
