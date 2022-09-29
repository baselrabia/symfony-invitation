<?php

namespace App\Tests;

use App\Entity\Invitation;
use App\Entity\User;
use App\Tests\AppBundle\DatabasePrimer;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\Exception\ORMException;
use Doctrine\ORM\OptimisticLockException;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class BaseTestCase extends KernelTestCase
{
    /**
     * @var EntityManager
     */
    protected $em;

    protected function setUp(): void
    {
        $kernel = self::bootKernel();
        DatabasePrimer::prime($kernel);
        $this->em = $kernel->getContainer()->get('doctrine')->getManager();
    }

    protected function tearDown(): void     {
        parent::tearDown();
        $this->em->close();
        $this->em = null;
    }


    /**
     * @throws OptimisticLockException
     * @throws ORMException
     */
    protected function createUser(string $email): User
    {
        $user = new User();
        $user->setEmail($email);
        $this->em->persist($user);
        $this->em->flush();
        return $user;
    }

    /**
     * @throws OptimisticLockException
     * @throws ORMException
     */
    protected function createInvitation($sender , $invite): Invitation
    {
        $invitation = new Invitation();
        $invitation->setTitle("weeding invitation");
        $invitation->setContent("we are honored to invite you for our party");
        $invitation->setSenderStatus("send");
        $invitation->setInvited($invite);
        $invitation->setSender($sender);
        $this->em->persist($invitation);
        $this->em->flush();
        return $invitation;
    }

}