<?php

namespace App\Repository;

use App\Entity\SearchInternship;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry;

/**
 * @method SearchInternship|null find($id, $lockMode = null, $lockVersion = null)
 * @method SearchInternship|null findOneBy(array $criteria, array $orderBy = null)
 * @method SearchInternship[]    findAll()
 * @method SearchInternship[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class SearchInternshipRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, SearchInternship::class);
    }

    // /**
    //  * @return SearchInternship[] Returns an array of SearchInternship objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('s')
            ->andWhere('s.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('s.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?SearchInternship
    {
        return $this->createQueryBuilder('s')
            ->andWhere('s.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
