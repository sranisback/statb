<?php

namespace App\Repository;

use App\Entity\HistoriqueBlessure;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method HistoriqueBlessure|null find($id, $lockMode = null, $lockVersion = null)
 * @method HistoriqueBlessure|null findOneBy(array $criteria, array $orderBy = null)
 * @method HistoriqueBlessure[]    findAll()
 * @method HistoriqueBlessure[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class HistoriqueBlessureRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, HistoriqueBlessure::class);
    }
}
