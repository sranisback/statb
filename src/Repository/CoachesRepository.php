<?php

namespace App\Repository;

use App\Entity\Coaches;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Coaches|null find($id, $lockMode = null, $lockVersion = null)
 * @method Coaches|null findOneBy(array $criteria, array $orderBy = null)
 * @method Coaches[]    findAll()
 * @method Coaches[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class CoachesRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Coaches::class);
    }

    /**
     * @param Coaches $coach
     * @return mixed
     */
    public function tousLesAutresCoaches(Coaches $coach)
    {
        return $this->createQueryBuilder('Coaches')
            ->where('Coaches.coachId !='.$coach->getCoachId())
            ->orderBy('Coaches.name')
            ->getQuery()->getResult();
    }
}
