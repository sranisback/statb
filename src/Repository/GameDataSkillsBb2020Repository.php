<?php

namespace App\Repository;

use App\Entity\GameDataSkillsBb2020;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method GameDataSkillsBb2020|null find($id, $lockMode = null, $lockVersion = null)
 * @method GameDataSkillsBb2020|null findOneBy(array $criteria, array $orderBy = null)
 * @method GameDataSkillsBb2020[]    findAll()
 * @method GameDataSkillsBb2020[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class GameDataSkillsBb2020Repository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, GameDataSkillsBb2020::class);
    }
}
