<?php

namespace App\Repository;

use App\Entity\Stades;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * @method Stades|null find($id, $lockMode = null, $lockVersion = null)
 * @method Stades|null findOneBy(array $criteria, array $orderBy = null)
 * @method Stades[]    findAll()
 * @method Stades[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class StadesRepository extends ServiceEntityRepository
{
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, Stades::class);
    }

    // /**
    //  * @return Stades[] Returns an array of Stades objects
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
    public function findOneBySomeField($value): ?Stades
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
