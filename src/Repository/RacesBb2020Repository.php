<?php

namespace App\Repository;

use App\Entity\RacesBb2020;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method RacesBb2020|null find($id, $lockMode = null, $lockVersion = null)
 * @method RacesBb2020|null findOneBy(array $criteria, array $orderBy = null)
 * @method RacesBb2020[]    findAll()
 * @method RacesBb2020[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class RacesBb2020Repository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, RacesBb2020::class);
    }
}
