<?php


namespace App\Service;

use App\Entity\Teams;

use Doctrine\Common\Persistence\ManagerRegistry;
class coachService
{
    private $doctrineEntityManager;

    public function __construct(ManagerRegistry $doctrineEntityManager)
    {
        $this->doctrineEntityManager = $doctrineEntityManager;
    }

    public function listeDesEquipeDuCoach($coach,$annee)
    {
        return $this->doctrineEntityManager->getRepository(Teams::class)->findBy(
            ['ownedByCoach' => $coach, 'year' =>$annee, 'retired' => false]
        );
    }

}