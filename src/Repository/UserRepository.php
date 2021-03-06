<?php

namespace App\Repository;

use App\Entity\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use League\OAuth2\Client\Provider\FacebookUser;

/**
 * @method User|null find($id, $lockMode = null, $lockVersion = null)
 * @method User|null findOneBy(array $criteria, array $orderBy = null)
 * @method User[]    findAll()
 * @method User[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class UserRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, User::class);
    }

    public function findOrCreateFromFacebookUser(FacebookUser $FacebookUser, $pageToken){

        $user = $this->createQueryBuilder('u')
            ->where('u.fbId = :fbId')
            ->setParameters([
                'fbId'=> $FacebookUser->getId()
            ])
            ->getQuery()
            ->getOneOrNullResult();
        if($user){
            return $user;
        }

        $user = (new User())
            ->setFbId($FacebookUser->getId())
            ->setUsername($FacebookUser->getName())
            ->setPageToken($pageToken);
        $em = $this->getEntityManager();
        $em->persist($user);
        $em->flush($user);
        return $user;
    }
    // /**
    //  * @return User[] Returns an array of User objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('u')
            ->andWhere('u.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('u.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    

    
}
