<?php

namespace App\Posts\Baha;

use App\Models\Poster;

/**
 * mainly to be an adapter for Poster model
 * in order to keep same [BahaPost]->save() syntax
 */
class PosterData
{
    /**
     * @property string
     */
    protected $account;

    /**
     * @property string
     */
    protected $name;

    /**
     * @property Poster
     */
    protected $poster;

    public function __construct($account, $name)
    {
        $this->account = $account;
        $this->name = (string) $name;
    }

    /**
     * save poster's data into database
     * or retrieve a existed row from it
     * if name is different, update the name
     *
     * @return Poster
     */
    public function save()
    {
        return $this->poster ??
        $this->poster = Poster::updateOrCreate([
            'account' => $this->account,
        ], [
            'name' => $this->name,
        ]);
    }
}
