<?php

namespace App\Service;

use App\Entity\User;
use Symfony\Component\Security\Core\Exception\AccountExpiredException;
use Symfony\Component\Security\Core\Exception\CustomUserMessageAccountStatusException;
use Symfony\Component\Security\Core\User\UserCheckerInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use Exception;

class UserChecker implements UserCheckerInterface
{
    public function checkPreAuth(UserInterface $user): void
    {        
        if ($user->getStatus() == User::PENDENTE) {
            throw new CustomUserMessageAccountStatusException('Your user account was not actived.', [ 
                'code' => 501,
            ]);
        }

        if ($user->getStatus() == User::INATIVO) {
            throw new CustomUserMessageAccountStatusException('Your user account was desabled.', [
                'code' => 502,
            ]);
        }
    }

    public function checkPostAuth(UserInterface $user): void
    {

    }
}