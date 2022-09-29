<?php

namespace App\Tests\feature;

use App\Entity\Invitation;
use App\Tests\BaseTestCase;
use Doctrine\ORM\Exception\ORMException;
use Doctrine\ORM\OptimisticLockException;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class InvitationActionsTest extends BaseTestCase
{
    private $invitationService;

    protected function setUp(): void
    {
        parent::setUp();
        $this->invitationService = self::$container->get('App\Service\InvitationService');

    }

    /**
     * @throws OptimisticLockException
     * @throws ORMException
     */
    public function test_sender_can_cancel_invitation(): void
    {
        // * Setup
        $user = $this->createUser("sender@gmail.com");

        $invitation = $this->invitationService->sendInvitation($user, [
            'title' => "party invitation",
            'content' => "we would like to invite you for our party",
            'invite' => "invited@gmail.com",
        ]);

        // * Action
        $this->assertEquals("send", $invitation->getSenderStatus());

        $this->invitationService->cancelInvitation($user, $invitation->getId());

        // * Assertion

        $this->assertEquals("cancel", $invitation->getSenderStatus());

    }


    /**
     * @throws OptimisticLockException
     * @throws ORMException
     */
    public function test_other_can_not_cancel_invitation(): void
    {
        // * Setup
        $user = $this->createUser("sender@gmail.com");
        $other = $this->createUser("other@gmail.com");

        $invitation = $this->invitationService->sendInvitation($user, [
            'title' => "party invitation",
            'content' => "we would like to invite you for our party",
            'invite' => "invited@gmail.com",
        ]);

        // * Action
        $this->expectExceptionMessage("UnAuthorized to Cancel the invitation");
        $this->invitationService->cancelInvitation($other, $invitation->getId());

        // * Assertion


    }


    /**
     * @throws OptimisticLockException
     * @throws ORMException
     */
    public function test_invited_can_accept_or_reject_invitation(): void
    {
        // * Setup
        $user = $this->createUser("sender@gmail.com");
        $invited = $this->createUser("invited@gmail.com");

        $invitation = $this->invitationService->sendInvitation($user, [
            'title' => "party invitation",
            'content' => "we would like to invite you for our party",
            'invite' => $invited->getEmail(),
        ]);

        // * Action

        $this->invitationService->acceptInvitation($invited, $invitation->getId());

        // * Assertion

        $this->assertEquals("accept", $invitation->getInvitedStatus());

        $this->invitationService->rejectInvitation($invited, $invitation->getId());

        $this->assertEquals("reject", $invitation->getInvitedStatus());

    }


    /**
     * @throws OptimisticLockException
     * @throws ORMException
     */
    public function test_invited_can_not_accept_canceled_invitation(): void
    {
        // * Setup
        $user = $this->createUser("sender@gmail.com");
        $invited = $this->createUser("invited@gmail.com");

        $invitation = $this->invitationService->sendInvitation($user, [
            'title' => "party invitation",
            'content' => "we would like to invite you for our party",
            'invite' => $invited->getEmail(),
        ]);

        // * Action
        $this->invitationService->cancelInvitation($user, $invitation->getId());

        // * Assertion

        $this->expectExceptionMessage("unable to Accept the invitation");
        $this->invitationService->acceptInvitation($invited, $invitation->getId());
    }

    /**
     * @throws OptimisticLockException
     * @throws ORMException
     */
    public function test_invited_can_not_reject_canceled_invitation(): void
    {
        // * Setup
        $user = $this->createUser("sender@gmail.com");
        $invited = $this->createUser("invited@gmail.com");

        $invitation = $this->invitationService->sendInvitation($user, [
            'title' => "party invitation",
            'content' => "we would like to invite you for our party",
            'invite' => $invited->getEmail(),
        ]);

        // * Action
        $this->invitationService->cancelInvitation($user, $invitation->getId());

        // * Assertion

        $this->expectExceptionMessage("unable to Reject the invitation");
        $this->invitationService->rejectInvitation($invited, $invitation->getId());
    }

    /**
     * @throws OptimisticLockException
     * @throws ORMException
     */
    public function test_other_can_not_accept_invitation(): void
    {
        // * Setup
        $user = $this->createUser("sender@gmail.com");
        $invited = $this->createUser("invited@gmail.com");
        $other = $this->createUser("other@gmail.com");

        $invitation = $this->invitationService->sendInvitation($user, [
            'title' => "party invitation",
            'content' => "we would like to invite you for our party",
            'invite' => $invited->getEmail(),
        ]);

        // * Action
        $this->expectExceptionMessage("unable to Accept the invitation");
        $this->invitationService->acceptInvitation($other, $invitation->getId());

    }

    /**
     * @throws OptimisticLockException
     * @throws ORMException
     */
    public function test_other_can_not_reject_invitation(): void
    {
        // * Setup
        $user = $this->createUser("sender@gmail.com");
        $invited = $this->createUser("invited@gmail.com");
        $other = $this->createUser("other@gmail.com");

        $invitation = $this->invitationService->sendInvitation($user, [
            'title' => "party invitation",
            'content' => "we would like to invite you for our party",
            'invite' => $invited->getEmail(),
        ]);

        // * Action
        $this->expectExceptionMessage("unable to Reject the invitation");
        $this->invitationService->rejectInvitation($other, $invitation->getId());

    }


}

