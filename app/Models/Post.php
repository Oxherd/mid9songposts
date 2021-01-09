<?php

namespace App\Models;

use App\Events\PostCreated;
use App\Links\LinkCollection;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;
use Symfony\Component\DomCrawler\Crawler;

class Post extends Model
{
    use HasFactory;

    /**
     * The event map for the model.
     *
     * @property array
     */
    protected $dispatchesEvents = [
        'created' => PostCreated::class,
    ];

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
     * @return \Illuminate\Database\Eloquent\Relations\MorphOne
     */
    public function noMusic()
    {
        return $this->morphOne(NoMusic::class, 'morph');
    }

    /**
     * extract all url string from content and save into Link
     *
     * make sure they are unique and domain register in lookup table
     *
     * @return void
     */
    public function extractLinks()
    {
        LinkCollection::fromText($this->content)
            ->unique()
            ->filter()
            ->get()
            ->each(function (Link $link) {
                $link->post_id = $this->id;
                $link->poster_id = $this->poster_id;
                $link->save();
            });
    }
}
