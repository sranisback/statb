<?php

namespace App\Repository;

use App\Entity\Coaches;
use App\Entity\Matches;
use App\Entity\Teams;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\NonUniqueResultException;
use Doctrine\Persistence\ManagerRegistry;

class MatchesRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Matches::class);
    }

    /**
     * @param int $annee
     * @return mixed
     */
    public function tousLesMatchDuneAnne(int $annee)
    {
        return $this->createQueryBuilder('m')
            ->join('m.team1', 't1')
            ->join('m.team2', 't2')
            ->where('t1.year =' . $annee)
            ->andWhere('t2.year =' . $annee)
            ->getQuery()
            ->getResult();
    }

    /**
     * @param int $annee
     * @param string $ordre
     * @return mixed
     */
    public function tousLesMatchDuneAnneClassementChrono(int $annee, string $ordre = 'DESC')
    {
        return $this->createQueryBuilder('m')
            ->join('m.team1', 't1')
            ->join('m.team2', 't2')
            ->where('t1.year =' . $annee)
            ->andWhere('t2.year =' . $annee)
            ->orderBy('m.dateCreated', $ordre)
            ->getQuery()
            ->getResult();
    }

    /**
     * @param Teams $equipe
     * @return array
     */
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

        if (!empty($matches)) {
            return $matches;
        }

        return [];
    }

    /**
     * @return int|mixed
     */
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


    /**
     * @param Coaches $coach1
     * @param Coaches $coach2
     * @return \Doctrine\ORM\QueryBuilder
     */
    public function tousLesMatchsDeDeuxCoach(Coaches $coach1, Coaches $coach2)
    {
        return $this->createQueryBuilder('Matches')
            ->join('Matches.team1', 'team1')
            ->join('Matches.team2', 'team2')
            ->join('team1.ownedByCoach', 'coach1')
            ->join('team2.ownedByCoach', 'coach2')
            ->where(
                '(coach1.coachId ='.$coach1->getCoachId().' AND coach2.coachId ='.$coach2->getCoachId().') OR
               (coach1.coachId ='.$coach2->getCoachId().' AND coach2.coachId ='.$coach1->getCoachId().') '
            )
            ->getQuery()
            ->execute();
    }
}
