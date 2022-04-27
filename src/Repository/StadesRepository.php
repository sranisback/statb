<?php

namespace App\Repository;

use App\Entity\Stades;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Stades|null find($id, $lockMode = null, $lockVersion = null)
 * @method Stades|null findOneBy(array $criteria, array $orderBy = null)
 * @method Stades[]    findAll()
 * @method Stades[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class StadesRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Stades::class);
    }
}
