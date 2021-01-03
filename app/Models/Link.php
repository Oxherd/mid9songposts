<?php

namespace App\Models;

use App\Links\Sites\SiteFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Link extends Model
{
    use HasFactory;

    /**
     * a mutator for other attributes when set 'original' attribute
     *
     * other words, by assign 'original' attribute, you get other attributes auto-filled
     *
     * @param stirng $value provide a protential url string
     *
     * @return void
     */
    public function setOriginalAttribute($value)
    {
        $this->attributes['original'] = $value;

        if (!Str::startsWith($value, 'http')) {
            $value = "https://" . $value;
        }

        $siteFactory = (new SiteFactory($value))->create();

        $this->attributes['site'] = $siteFactory->name();
        $this->attributes['resource_id'] = $siteFactory->getResourceId();
    }

    public function general()
    {
        if (!$this->attributes['resource_id']) {
            return null;
        }

        $site = (new SiteFactory($this->attributes['original']))->create();

        return $site::generalUrl($this->resource_id);
    }
}
