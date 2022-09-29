<?php

namespace App\Tests\feature;

use App\Entity\Invitation;
use App\Entity\User;
use App\Tests\BaseTestCase;
use Doctrine\ORM\Exception\ORMException;
use Doctrine\ORM\OptimisticLockException;

class InvitationTest extends BaseTestCase
{

    /**
     * @throws OptimisticLockException
     * @throws ORMException
     */
    public function test_invitation_can_store_in_DB(): void
    {
        // * Setup
        $invitation = new Invitation();
        $invitation->setTitle("weeding invitation");
        $invitation->setContent("we are honored to invite you for our party");
        $invitation->setSenderStatus("send");

        $invite = $this->createUser("invited@gmail.com");
        $sender = $this->createUser("sender@gmail.com");

        $invitation->setInvited($invite);
        $invitation->setSender($sender);
        $this->em->persist($invitation);

        // * Action
        $this->em->flush();

        $invitationRepo = $this->em->getRepository(Invitation::class);
        $invitationRecord  = $invitationRepo->findOneBy(['title' => 'weeding invitation']);

        // * Assertion
        $output = $invitationRecord->getContent();

        $expected = "we are honored to invite you for our party";

        $this->assertEquals($expected, $output);

    }



}
