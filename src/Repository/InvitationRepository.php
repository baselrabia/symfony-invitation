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
          $entity =  $this->createQueryBuilder('i')
                ->andWhere('i.id = :val')
                ->setParameter('val', $id)
                ->getQuery()
                ->getOneOrNullResult();

          if($entity){
              $sender = $entity->getSender()->getEmail();
              $invited = $entity->getInvited()->getEmail();
              $authorize =  $user->getEmail()  == $sender ||  $user->getEmail()  == $invited ;
              if ($authorize) {
                  return $entity;
              }
              Throw new AccessDeniedHttpException("UnAuthorized access to the Entity " . $id);
          }
        return null;
    }

    /**
     * @throws NonUniqueResultException
     */
    public function cancel($user, $id)
    {
        $entity  = $this->getEntity($user, $id);
        if(! $entity){
            throw new NotFoundHttpException("there is no entity with that id " . $id);
        }
        $entity->setSenderStatus("cancel");
        $this->_em->flush();

        return $entity;
    }

}
