<?php

namespace App\EntityListener;

use App\Entity\User;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasher;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;


class UserListener
{

    private UserPasswordHasherInterface $hasher;
    public function __construct(UserPasswordHasherInterface $hasher)
    {
        $this->hasher = $hasher;
    }
    public function prePersist(User $user)
    {
        $this->encodePassWord($user);
    }


    /**
     * La fonction encode le mots de passe user grâce a PlainPassword
     * @param User $user
     * @return void
     */
    public function encodePassWord(User $user)
    {
        if ($user->getPlainPassword() === null)
        {
            return;
        }
        $user->setPassword(
            $this->hasher->hashPassword(
                $user,
                $user->getPlainPassword()
            )

        );

        $user->setPlainPassword(null);
    }


}