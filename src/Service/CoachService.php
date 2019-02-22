<?php


namespace App\Service;

use App\Entity\Teams;

use Doctrine\ORM\EntityManagerInterface;

class CoachService
{
    private $doctrineEntityManager;

    public function __construct(EntityManagerInterface $doctrineEntityManager)
    {
        $this->doctrineEntityManager = $doctrineEntityManager;
    }

    public function listeDesEquipeDuCoach($coach, $annee)
    {
        return $this->doctrineEntityManager->getRepository(Teams::class)->findBy(
            ['ownedByCoach' => $coach, 'year' =>$annee]
        );
    }
}
