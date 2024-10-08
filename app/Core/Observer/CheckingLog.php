<?php

namespace App\Core\Observer;

use App\Core\Observer\CheckingLogObserver;

trait CheckingLog
{
    public static function bootCreating() {
        static::observe(CheckingLogObserver::class);
    }
}
