<?php

namespace App\Service;

use App\Repository\InvitationRepository;
use App\Entity\User;
use Doctrine\ORM\NonUniqueResultException;

class InvitationService
{
    private $invitationRepo;

    public function __construct(InvitationRepository $invitationRepository)
    {
        $this->invitationRepo = $invitationRepository;
    }

    public function listSenderInvitations(User $user, string $status)
    {
        return $this->invitationRepo->listSenderInvitations($user, $status);
    }

    public function listInvitedInvitations(User $user, string $status)
    {
        return $this->invitationRepo->listInvitedInvitations($user, $status);
    }

    public function sendInvitation(User $user, array $data)
    {
        return $this->invitationRepo->create($user, $data);
    }

    public function getInvitation($user, $id)
    {
        return $this->invitationRepo->getEntity($user, $id);
    }

    /**
     * @throws NonUniqueResultException
     */
    public function cancelInvitation($user, $id)
    {
        return $this->invitationRepo->cancel($user, $id);
    }

    public function acceptInvitation($user, $id)
    {
        return $this->invitationRepo->accept($user, $id);
    }

    public function rejectInvitation($user, $id)
    {
        return $this->invitationRepo->reject($user, $id);

    }

}