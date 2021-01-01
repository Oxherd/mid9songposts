<?php

namespace App\Models;

use App\Events\PostCreated;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

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
}
