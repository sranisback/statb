<?php

namespace App\Repository;

use App\Entity\GameDataStades;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * @method GameDataStades|null find($id, $lockMode = null, $lockVersion = null)
 * @method GameDataStades|null findOneBy(array $criteria, array $orderBy = null)
 * @method GameDataStades[]    findAll()
 * @method GameDataStades[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class GameDataStadesRepository extends ServiceEntityRepository
{
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, GameDataStades::class);
    }

    // /**
    //  * @return GameDataStades[] Returns an array of GameDataStades objects
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
    public function findOneBySomeField($value): ?GameDataStades
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
