<?php

namespace App\Repository;

use App\Entity\Dyk;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Dyk|null find($id, $lockMode = null, $lockVersion = null)
 * @method Dyk|null findOneBy(array $criteria, array $orderBy = null)
 * @method Dyk[]    findAll()
 * @method Dyk[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class DykRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Dyk::class);
    }
}
