<?php
namespace App\Core\Observer;

use App\Core\Observer\ClaimObserver;

trait Claim
{
    public static function bootClaim() {
        static::observe(ClaimObserver::class);
    }
}
