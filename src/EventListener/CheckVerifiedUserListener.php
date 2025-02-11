<?php

namespace App\EventListener;

use Symfony\Component\Security\Http\Event\CheckPassportEvent;
use Symfony\Component\EventDispatcher\Attribute\AsEventListener;
use Symfony\Component\Security\Core\Exception\CustomUserMessageAuthenticationException;

class CheckVerifiedUserListener
{
    #[AsEventListener(CheckPassportEvent::class)]
    public function onCheckPassport(CheckPassportEvent $event): void
    {
        $passport = $event->getPassport();
        $user = $passport->getUser();
        
        if (!$user->isVerified()) {
            throw new CustomUserMessageAuthenticationException('account.not_verified', [], 0);
        }
    }
}
