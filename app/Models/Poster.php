<?php

namespace App\Models;

use App\Jobs\ScrapePostsFromSearchUser;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Poster extends Model
{
    use HasFactory;

    /**
     * The "booted" method of the model.
     *
     * hooking model event
     *
     * @return void
     */
    protected static function booted()
    {
        static::created(function ($poster) {
            ScrapePostsFromSearchUser::dispatch(
                $poster->account,
                $switchPage = true
            );
        });
    }

    /**
     * @return Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function posts()
    {
        return $this->hasMany(Post::class);
    }
}
