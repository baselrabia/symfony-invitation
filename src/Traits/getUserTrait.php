<?php

namespace App\Traits;

use App\Entity\User;

trait getUserTrait
{
    public function getUserWithEmail($email): User
    {
        $userRepo = $this->getDoctrine()->getRepository(User::class);
        return $this->findOrCreate($userRepo, $email);
    }

    public function findOrCreate($userRepo , $email): User
    {
        $user = $userRepo->findOneBy(['email' => $email]);

        if(!$user){
            $user = new User;
            $user->setEmail($email);
            $userRepo->add($user);
        }

        return $user;
    }



}