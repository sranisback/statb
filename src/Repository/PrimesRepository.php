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
            ->join('Primes.players', 'players')
            ->join('players.ownedByTeam', 'teams')
            ->where('teams.year = '.$annee)
            ->getQuery()
            ->getResult();
    }
}
