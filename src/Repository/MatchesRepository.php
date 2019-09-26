<?php

namespace App\Repository;

use App\Entity\Matches;
use App\Entity\Teams;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\NonUniqueResultException;
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
            ->join('m.team1', 't1')
            ->join('m.team2', 't2')
            ->where('t1.year =' . $annee)
            ->andWhere('t2.year =' . $annee)
            ->getQuery()
            ->getResult();
    }

    public function tousLesMatchDuneAnneClassementChrono($annee)
    {
        return $this->createQueryBuilder('m')
            ->join('m.team1', 't1')
            ->join('m.team2', 't2')
            ->where('t1.year =' . $annee)
            ->andWhere('t2.year =' . $annee)
            ->orderBy('m.dateCreated', 'DESC')
            ->getQuery()
            ->getResult();
    }

    public function listeDesMatchs(Teams $equipe)
    {
        $matches1 = $this->getEntityManager()->getRepository(Matches::class)->findBy(
            ['team1' => $equipe->getTeamId()],
            ['dateCreated' => 'DESC']
        );

        $matches2 = $this->getEntityManager()->getRepository(Matches::class)->findBy(
            ['team2' => $equipe->getTeamId()],
            ['dateCreated' => 'DESC']
        );

        $matches = array_merge($matches1, $matches2);

        usort(
            $matches,
            function ($a, $b) {
                $ad = $a->getDateCreated();
                $bd = $b->getDateCreated();

                if ($ad == $bd) {
                    return 0;
                }

                return $ad > $bd ? -1 : 1;
            }
        );

        return $matches;
    }

    public function numeroDeMatch()
    {
        try {
            return $this->createQueryBuilder('m')
                ->select('MAX(m.matchId)')
                ->getQuery()
                ->getSingleScalarResult();
        } catch (NonUniqueResultException $e) {
        }

        return 0;
    }
}
