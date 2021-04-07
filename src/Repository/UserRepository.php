<?php

namespace App\Repository;

use App\Entity\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry;

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

    public function getNbUser()
    {
        return $this->createQueryBuilder('u')
            ->select('COUNT(u.id) as nb')
            ->getQuery()
            ->getSingleScalarResult();
    }

    public function getModo()
    {
        return $this->createQueryBuilder('u')
            ->select('u')
            ->where('u.roles IN (:admin, :modo)')
            ->setParameter('admin', 'a:1:{i:0;s:10:"ROLE_ADMIN";}')
            ->setParameter('modo', 'a:1:{i:0;s:9:"ROLE_MODO";}')
            ->getQuery()
            ->getResult();
    }
}
