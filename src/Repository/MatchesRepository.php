<?php

namespace App\Repository;

use App\Entity\Matches;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;


class MatchesRepository extends ServiceEntityRepository
{
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, Matches::class);
    }

    public function tousLesMatchDuneAnne($annee)
    {
        return $this->createQueryBuilder('m')
            ->join('m.team1','t1')
            ->join('m.team2','t2')
            ->where('t1.year = 3')
            ->andWhere('t2.year ='.$annee)
            ->getQuery()
            ->getResult();
    }

}
