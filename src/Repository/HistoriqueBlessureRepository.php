<?php

namespace App\Repository;

use App\Entity\HistoriqueBlessure;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method HistoriqueBlessure|null find($id, $lockMode = null, $lockVersion = null)
 * @method HistoriqueBlessure|null findOneBy(array $criteria, array $orderBy = null)
 * @method HistoriqueBlessure[]    findAll()
 * @method HistoriqueBlessure[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class HistoriqueBlessureRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, HistoriqueBlessure::class);
    }

    // /**
    //  * @return HistoriqueBlessure[] Returns an array of HistoriqueBlessure objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('h')
            ->andWhere('h.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('h.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?HistoriqueBlessure
    {
        return $this->createQueryBuilder('h')
            ->andWhere('h.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
