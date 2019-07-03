<?php

namespace App\Repository;

use App\Entity\Citations;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * @method Citations|null find($id, $lockMode = null, $lockVersion = null)
 * @method Citations|null findOneBy(array $criteria, array $orderBy = null)
 * @method Citations[]    findAll()
 * @method Citations[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class CitationsRepository extends ServiceEntityRepository
{
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, Citations::class);
    }

    // /**
    //  * @return Citations[] Returns an array of Citations objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('c')
            ->andWhere('c.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('c.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?Citations
    {
        return $this->createQueryBuilder('c')
            ->andWhere('c.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
