<?php

namespace App\Repository;

use App\Entity\DeadPlayerInfo;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<DeadPlayerInfo>
 *
 * @method DeadPlayerInfo|null find($id, $lockMode = null, $lockVersion = null)
 * @method DeadPlayerInfo|null findOneBy(array $criteria, array $orderBy = null)
 * @method DeadPlayerInfo[]    findAll()
 * @method DeadPlayerInfo[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class DeadPlayerInfoRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, DeadPlayerInfo::class);
    }

    public function add(DeadPlayerInfo $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(DeadPlayerInfo $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

//    /**
//     * @return DeadPlayerInfo[] Returns an array of DeadPlayerInfo objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('d')
//            ->andWhere('d.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('d.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?DeadPlayerInfo
//    {
//        return $this->createQueryBuilder('d')
//            ->andWhere('d.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
