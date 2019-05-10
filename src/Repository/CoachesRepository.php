<?php

namespace App\Repository;

use App\Entity\Coaches;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * @method Coaches|null find($id, $lockMode = null, $lockVersion = null)
 * @method Coaches|null findOneBy(array $criteria, array $orderBy = null)
 * @method Coaches[]    findAll()
 * @method Coaches[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class CoachesRepository extends ServiceEntityRepository
{
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, Coaches::class);
    }
}
