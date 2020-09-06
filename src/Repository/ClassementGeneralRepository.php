<?php

namespace App\Repository;

use App\Entity\ClassementGeneral;
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

    public function classementGeneral($annee)
    {
        return $this->createQueryBuilder('cg')
            ->select(
                'cg total','cg.gagne+cg.egalite+cg.perdu nbr', 'cg.points + cg.bonus pointTotaux')
            ->join('cg.equipe', 'equipe')
            ->where('equipe.year =' . $annee)
            ->addOrderBy('pointTotaux','DESC')
            ->addOrderBy('nbr','ASC')
            ->getQuery()->execute();
    }

    // /**
    //  * @return ClassementGeneral[] Returns an array of ClassementGeneral objects
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
    public function findOneBySomeField($value): ?ClassementGeneral
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
