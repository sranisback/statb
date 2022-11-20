<?php

namespace App\Repository;

use App\Entity\ClassementGeneral;
use App\Entity\Teams;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method ClassementGeneral|null find($id, $lockMode = null, $lockVersion = null)
 * @method ClassementGeneral|null findOneBy(array $criteria, array $orderBy = null)
 * @method ClassementGeneral[]    findAll()
 * @method ClassementGeneral[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ClassementGeneralRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, ClassementGeneral::class);
    }

    public function classementGeneral(int $annee)
    {
        return $this->createQueryBuilder('cg')
            ->select(
                'cg total',
                'cg.gagne+cg.egalite+cg.perdu nbr',
                'cg.points + cg.bonus-cg.penalite pointTotaux'
            )
            ->join('cg.equipe', 'equipe')
            ->where('equipe.year =' . $annee)
            ->andWhere('cg.gagne+cg.egalite+cg.perdu > 0')
            ->addOrderBy('pointTotaux', 'DESC')
            ->addOrderBy('nbr', 'ASC')
            ->getQuery()->execute();
    }

    public function classementGeneralDetail(int $annee)
    {
        return $this->createQueryBuilder('cg')
            ->select(
                'cg total',
                'cg.tdPour - cg.tdContre tdAverage',
                'cg.casPour - cg.casContre casAverage'
            )
            ->join('cg.equipe', 'equipe')
            ->where('equipe.year =' . $annee)
            ->addOrderBy('equipe.name', 'ASC')
            ->getQuery()->execute();
    }

    public function scoreDuneEquipe(Teams $equipe)
    {
        return $this->createQueryBuilder('cg')
            ->select('cg.points')
            ->where('cg.equipe = ' . $equipe->getTeamId())
            ->getQuery()->getSingleScalarResult();
    }

    public function scoreDetailDuneEquipe(Teams $equipe)
    {
        return $this->createQueryBuilder('cg')
            ->where('cg.equipe = ' . $equipe->getTeamId())
            ->getQuery()->getResult();
    }
}
