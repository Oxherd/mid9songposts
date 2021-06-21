<?php

namespace App\Models;

use App\Events\PostCreated;
use App\Links\LinkCollection;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Post extends Model
{
    use HasFactory;

    /**
     * The attributes that should be cast.
     *
     * @var array
     */
    protected $casts = [
        'has_music' => 'boolean',
    ];

    protected static function booted()
    {
        static::created(function($post) {
            $post->extractLinks();
        });
    }

    /**
     * @return Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function poster()
    {
        return $this->belongsTo(Poster::class);
    }

    /**
     * @return \Illuminate\Database\Eloquent\Concerns\HasRelationships
     */
    public function links()
    {
        return $this->hasMany(Link::class);
    }

    /**
     * extract all url string from content and save them into Link
     *
     * in case link get duplicated, delete associate links first
     *
     * see test in Tests\Feature\Listeners\ExtracContentLinksTest.php
     *
     * @return void
     */
    public function extractLinks()
    {
        $links = LinkCollection::fromText($this->content)
            ->unique()
            ->filter()
            ->get();

        if ($links->isNotEmpty()) {
            $this->links()->delete();

            $links->each(function (Link $link) {
                $link->post_id = $this->id;
                $link->poster_id = $this->poster_id;
                $link->save();
            });
        }

        $this->update(['has_music' => $links->isNotEmpty()]);
    }
}
