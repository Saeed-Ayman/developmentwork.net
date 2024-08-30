<?php

namespace App\Observers;

class StatusObserver
{
    public function created(): void
    {
        \Cache::flush();
    }

    public function forceDeleted(): void
    {
        // when using something like redis
        // \Cache::tags('status')->flush();

        \Cache::flush();
    }
}
