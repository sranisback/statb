<?php

namespace App\Repository;

use App\Entity\GameDataPlayersBb2020;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method GameDataPlayersBb2020|null find($id, $lockMode = null, $lockVersion = null)
 * @method GameDataPlayersBb2020|null findOneBy(array $criteria, array $orderBy = null)
 * @method GameDataPlayersBb2020[]    findAll()
 * @method GameDataPlayersBb2020[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class GameDataPlayersBb2020Repository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, GameDataPlayersBb2020::class);
    }

    // /**
    //  * @return GameDataPlayersBb2020[] Returns an array of GameDataPlayersBb2020 objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('g')
            ->andWhere('g.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('g.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?GameDataPlayersBb2020
    {
        return $this->createQueryBuilder('g')
            ->andWhere('g.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
