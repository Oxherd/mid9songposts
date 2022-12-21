<?php

namespace App\Models;

use App\Jobs\ScrapePostsFromSearchUserContinuously;
use Illuminate\Database\Eloquent\Casts\Attribute;
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
            ScrapePostsFromSearchUserContinuously::dispatch(
                $poster->account
            );
        });
    }

    protected function avatar(): Attribute
    {
        return Attribute::make(function ($value, $attributes) {
            $account = strtolower($attributes['account']);
            return "https://avatar2.bahamut.com.tw/avataruserpic/{$account[0]}/{$account[1]}/{$account}/{$account}_s.png";
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
