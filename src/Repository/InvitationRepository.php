<?php

namespace App\Repository;

use App\Entity\Invitation;
use App\Traits\getUserTrait;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\NonUniqueResultException;
use Doctrine\ORM\OptimisticLockException;
use Doctrine\ORM\ORMException;
use Doctrine\Persistence\ManagerRegistry;
use App\Entity\User;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;


/**
 * @extends ServiceEntityRepository<Invitation>
 *
 * @method Invitation|null find($id, $lockMode = null, $lockVersion = null)
 * @method Invitation|null findOneBy(array $criteria, array $orderBy = null)
 * @method Invitation[]    findAll()
 * @method Invitation[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class InvitationRepository extends ServiceEntityRepository
{
    use getUserTrait;

    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Invitation::class);
    }


    public function listSenderInvitations(User $user, $status)
    {
        return $user->getSendInvitations();
    }

    public function listInvitedInvitations(User $user, $status)
    {
        return $user->getReceivedInvitations();
    }

    /**
     * @throws ORMException
     * @throws OptimisticLockException
     */
    public function add(Invitation $entity, bool $flush = true): void
    {
        $this->_em->persist($entity);
        if ($flush) {
            $this->_em->flush();
        }
    }

    /**
     * @throws ORMException
     * @throws OptimisticLockException
     */
    public function remove(Invitation $entity, bool $flush = true): void
    {
        $this->_em->remove($entity);
        if ($flush) {
            $this->_em->flush();
        }
    }

    // /**
    //  * @return Invitation[] Returns an array of Invitation objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('i')
            ->andWhere('i.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('i.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?Invitation
    {
        return $this->createQueryBuilder('i')
            ->andWhere('i.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
    /**
     * @throws OptimisticLockException
     * @throws ORMException
     */
    public function create(User $user, array $data): Invitation
    {
        $invitation = new Invitation();
        $invitation->setTitle($data['title']);
        $invitation->setContent($data['content']);
        $invitation->setSenderStatus("send");

        $invite = $this->getInviteUser($data['invite']);
        $invitation->setInvited($invite);

        $user->addSendInvitation($invitation);

        $this->add($invitation);
        return $invitation;
    }

    private function getInviteUser($email): User
    {
        $userRepo = $this->_em->getRepository(User::class);
        return $this->findOrCreate($userRepo, $email);
    }

    /**
     * @throws NonUniqueResultException
     * @throws AccessDeniedHttpException
     */
    public function getEntity($user, $id): ?Invitation
    {
        $entity = $this->createQueryBuilder('i')
            ->andWhere('i.id = :val')
            ->setParameter('val', $id)
            ->getQuery()
            ->getOneOrNullResult();

        if ($entity) {
            $sender = $entity->getSender()->getEmail();
            $invited = $entity->getInvited()->getEmail();
            $authorize = $user->getEmail() == $sender || $user->getEmail() == $invited;
            if ($authorize) {
                return $entity;
            }
            throw new AccessDeniedHttpException("UnAuthorized access to the invitation " . $id);
        }
        return null;
    }

    /**
     * @throws NonUniqueResultException
     */
    public function cancel($user, $id): ?Invitation
    {
        $entity = $this->getEntity($user, $id);

        $this->Authorize($user, $entity, "cancel");

        $entity->setSenderStatus("cancel");
        $this->_em->flush();

        return $entity;
    }

    public function accept($user, $id): ?Invitation
    {
        $entity = $this->getEntity($user, $id);

        $this->Authorize($user, $entity, "accept");

        $entity->setInvitedStatus("accept");
        $this->_em->flush();

        return $entity;
    }

    public function reject($user, $id): ?Invitation
    {
        $entity = $this->getEntity($user, $id);
        $this->Authorize($user, $entity, "reject");

        $entity->setInvitedStatus("reject");
        $this->_em->flush();

        return $entity;
    }


    private function Authorize($user, $entity, $action)
    {
        if (!$entity) {
            throw new NotFoundHttpException("there is no entity");
        }

        $sender = $entity->getSender()->getEmail();
        $senderStatus = $entity->getSenderStatus();
        $invited = $entity->getInvited()->getEmail();
        $currentUser = $user->getEmail();

        $authorize = [
            'cancel' => [
                "value" => $currentUser == $sender,
                "err_msg" => "UnAuthorized to Cancel the invitation "
            ],
            'accept' => [
                "value" => $currentUser == $invited && $senderStatus == "send",
                "err_msg" => "Invitation is canceled while trying to Accept the invitation "
            ],
            'reject' => [
                "value" => $currentUser == $invited && $senderStatus == "send",
                "err_msg" => "Invitation is canceled while trying to Reject the invitation "
            ],
        ];

        if (!$authorize[$action]["value"]) {
            throw new AccessDeniedHttpException($authorize[$action]["err_msg"]);
        }

    }

}
