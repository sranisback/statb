<?php

namespace App\Repository;

use App\Entity\Infos;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Infos|null find($id, $lockMode = null, $lockVersion = null)
 * @method Infos|null findOneBy(array $criteria, array $orderBy = null)
 * @method Infos[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class InfosRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Infos::class);
    }

    public function cinqDernieresNews()
    {
        return $this->createQueryBuilder('infos')
            ->setMaxResults(5)
            ->orderBy('infos.date', 'DESC')
            ->getQuery()
            ->getResult();
    }

    public function findAll()
    {
        return $this->createQueryBuilder('infos')
            ->orderBy('infos.date', 'DESC')
            ->getQuery()
            ->getResult();
    }
}
