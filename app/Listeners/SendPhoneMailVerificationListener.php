<?php

namespace App\Listeners;

use App\Events\RegisterEvent;

class SendPhoneMailVerificationListener
{
    public function handle(RegisterEvent $event): void
    {
        \Log::driver('phone-verification')
            ->info('verification code: '.$event->user->generateVerifiedCode());
    }
}
