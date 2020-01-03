<?php

namespace App\Repository;

use App\Entity\Primes;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Primes|null find($id, $lockMode = null, $lockVersion = null)
 * @method Primes|null findOneBy(array $criteria, array $orderBy = null)
 * @method Primes[]    findAll()
 * @method Primes[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class PrimesRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Primes::class);
    }

    public function listePrimeEnCours($annee)
    {
        return $this->createQueryBuilder('Primes')
            ->join('Primes.teams', 'teams')
            ->where('teams.year = '.$annee)
            ->getQuery()
            ->getResult();
    }

    // /**
    //  * @return Primes[] Returns an array of Primes objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('p')
            ->andWhere('p.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('p.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?Primes
    {
        return $this->createQueryBuilder('p')
            ->andWhere('p.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
