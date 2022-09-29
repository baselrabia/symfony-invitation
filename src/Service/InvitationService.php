<?php

namespace App\Service;

use App\Entity\Invitation;
use App\Repository\InvitationRepository;
use App\Entity\User;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\NonUniqueResultException;
use Doctrine\ORM\OptimisticLockException;
use Doctrine\ORM\ORMException;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class InvitationService
{
    private $invitationRepo;

    public function __construct(InvitationRepository $invitationRepository)
    {
        $this->invitationRepo = $invitationRepository;
    }

    public function listSenderInvitations(User $user, string $status): Collection
    {
        return $this->invitationRepo->listSenderInvitations($user, $status);
    }

    public function listInvitedInvitations(User $user, string $status): Collection
    {
        return $this->invitationRepo->listInvitedInvitations($user, $status);
    }

    /**
     * @throws OptimisticLockException
     * @throws ORMException
     */
    public function sendInvitation(User $user, array $data): Invitation
    {
        return $this->invitationRepo->create($user, $data);
    }

    /**
     * @throws NonUniqueResultException
     */
    public function getInvitation($user, $id): ?Invitation
    {
       $entity =  $this->invitationRepo->getEntity($id);

       $this->Authorize($user,"view", $entity);

       return $entity;
    }

    /**
     * @throws NonUniqueResultException
     */
    public function cancelInvitation($user, $id): ?Invitation
    {
        $entity = $this->invitationRepo->getEntity($id);

        $this->Authorize($user, "cancel", $entity);

        return $this->invitationRepo->cancel($entity);
    }


    /**
     * @throws NonUniqueResultException
     */
    public function acceptInvitation($user, $id): ?Invitation
    {
        $entity = $this->invitationRepo->getEntity($id);

        $this->Authorize($user, "accept", $entity);

        return $this->invitationRepo->accept($entity);
    }


    /**
     * @throws NonUniqueResultException
     */
    public function rejectInvitation($user, $id): ?Invitation
    {
        $entity = $this->invitationRepo->getEntity($id);

        $this->Authorize($user, "reject", $entity);

        return $this->invitationRepo->reject($entity);
    }


    private function Authorize($user, $action, $entity): void
    {
        if (!$entity) {
            throw new NotFoundHttpException("there is no entity");
        }

        $sender = $entity->getSender()->getEmail();
        $senderStatus = $entity->getSenderStatus();
        $invited = $entity->getInvited()->getEmail();
        $currentUser = $user->getEmail();

        $authorize = [
            'view' => [
                "value" => $currentUser == $sender || $currentUser == $invited,
                "err_msg" => "UnAuthorized access to view this invitation"
            ],
            'cancel' => [
                "value" => $currentUser == $sender,
                "err_msg" => "UnAuthorized to Cancel the invitation"
            ],
            'accept' => [
                "value" => $currentUser == $invited && $senderStatus == "send",
                "err_msg" => "Invitation is canceled while trying to Accept the invitation"
            ],
            'reject' => [
                "value" => $currentUser == $invited && $senderStatus == "send",
                "err_msg" => "Invitation is canceled while trying to Reject the invitation"
            ],
        ];

        if (!$authorize[$action]["value"]) {
            throw new AccessDeniedHttpException($authorize[$action]["err_msg"]);
        }

    }
}