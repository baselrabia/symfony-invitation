<?php

namespace App\Tests\feature;

use App\Entity\Invitation;
use App\Tests\BaseTestCase;
use Doctrine\ORM\Exception\ORMException;
use Doctrine\ORM\OptimisticLockException;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class InvitationServiceTest extends BaseTestCase
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
    public function test_user_can_send_invitation(): void
    {
        // * Setup
        $user = $this->createUser("sender@gmail.com");

        $invitation= $this->invitationService->sendInvitation($user, [
            'title' => "party invitation",
            'content' => "we would like to invite you for our party",
            'invite' => 'invited@gmail.com',
        ]);

        // * Action

        $invitationRepo = $this->em->getRepository(Invitation::class);
        $invitationRecord  = $invitationRepo->findOneBy(['title' => 'party invitation']);

        // * Assertion
        $output = $invitationRecord->getContent();
        $expected = "we would like to invite you for our party";

        $this->assertEquals($expected, $output);
    }


    /**
     * @throws OptimisticLockException
     * @throws ORMException
     */
    public function test_invitation_exist_in_list_sender_invitations(): void
    {
        // * Setup
        $user = $this->createUser("sender@gmail.com");

        $this->invitationService->sendInvitation($user, [
            'title' => "party invitation",
            'content' => "we would like to invite you for our party",
            'invite' => 'invited@gmail.com',
        ]);
        $this->invitationService->sendInvitation($user, [
            'title' => "party invitation",
            'content' => "we would like to invite you for our party",
            'invite' => 'ahmed@gmail.com',
        ]);

        $this->invitationService->sendInvitation($user, [
            'title' => "party invitation",
            'content' => "we would like to invite you for our party",
            'invite' => 'sara@gmail.com',
        ]);

        // * Action

        $senderInviationList = $this->invitationService->listSenderInvitations($user,"all");
        $invitationRecord  = $senderInviationList->first();

        // * Assertion
        $this->assertCount(3, $senderInviationList);

        $output = $invitationRecord->getContent();
        $expected = "we would like to invite you for our party";

        $this->assertEquals($expected, $output);
    }


    /**
     * @throws OptimisticLockException
     * @throws ORMException
     */
    public function test_invited_receive_the_send_invitation(): void
    {
        // * Setup
        $user = $this->createUser("sender@gmail.com");
        $invited = $this->createUser("invited@gmail.com");

        $this->invitationService->sendInvitation($user, [
            'title' => "party invitation",
            'content' => "we would like to invite you for our party",
            'invite' => $invited->getEmail(),
        ]);

        // * Action

        $invitedListInvitations = $this->invitationService->listInvitedInvitations($invited,"all");
        $senderlistInvitations = $this->invitationService->listInvitedInvitations($user,"all");
        $invitationRecord  = $invitedListInvitations->first();

        // * Assertion
        $this->assertCount(1, $invitedListInvitations);
        $this->assertCount(0, $senderlistInvitations);

        $output = $invitationRecord->getContent();
        $expected = "we would like to invite you for our party";

        $this->assertEquals($expected, $output);
    }


    /**
     * @throws OptimisticLockException
     * @throws ORMException
     */
    public function test_able_to_view_invitation(): void
    {
        // * Setup
        $user = $this->createUser("sender@gmail.com");
        $invited = $this->createUser("invited@gmail.com");
        $forignUser = $this->createUser("forign@gmail.com");

        $invitation = $this->invitationService->sendInvitation($user, [
            'title' => "party invitation",
            'content' => "we would like to invite you for our party",
            'invite' => $invited->getEmail(),
        ]);

        // * Action
        $this->expectExceptionMessage("UnAuthorized access to view this invitation");
        $foreignUser_invitation = $this->invitationService->getInvitation($forignUser, $invitation->getId());

    }

    /**
     * @throws OptimisticLockException
     * @throws ORMException
     */
    public function test_view_same_invitation(): void
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

        $user_invitation = $this->invitationService->getInvitation($user, $invitation->getId());
        $invited_invitation = $this->invitationService->getInvitation($invited, $invitation->getId());

        // * Assertion

        $this->assertEquals($user_invitation->getTitle(), $invited_invitation->getTitle());

    }





}

