<?php

namespace App\Helpers;

use Illuminate\Support\Facades\App;

class Pause
{
    /**
     * @param int $seconds determine how long php will sleep
     *
     * @return void
     */
    public static function seconds($seconds = 2)
    {
        if (App::environment() !== 'testing') {
            sleep($seconds);
        }
    }
}
