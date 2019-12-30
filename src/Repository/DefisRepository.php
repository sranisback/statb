<?php

namespace App\Repository;

use App\Entity\Defis;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Defis|null find($id, $lockMode = null, $lockVersion = null)
 * @method Defis|null findOneBy(array $criteria, array $orderBy = null)
 * @method Defis[]    findAll()
 * @method Defis[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class DefisRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Defis::class);
    }

    public function listeDefisEnCours($annee)
    {
        return $this->createQueryBuilder('Defis')
            ->join('Defis.equipeOrigine', 'teams')
            ->where('teams.year = '.$annee)
            ->getQuery()
            ->getResult();
    }

    public function listeDeDefisActifPourLeMatch($equipe1Id, $equipe2Id)
    {
        return $this->createQueryBuilder('Defis')
            ->where(
                '((Defis.equipeOrigine ='.$equipe1Id.' AND Defis.equipeDefiee = '.$equipe2Id.') 
                                OR (Defis.equipeOrigine ='.$equipe2Id.'  AND Defis.equipeDefiee = '.$equipe1Id.'))
                                AND Defis.defieRealise = 0'
            )
            ->getQuery()
            ->getOneOrNullResult();
    }

    // /**
    //  * @return Defis[] Returns an array of Defis objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('d')
            ->andWhere('d.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('d.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?Defis
    {
        return $this->createQueryBuilder('d')
            ->andWhere('d.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
