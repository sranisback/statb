<?php

namespace App\Service;

use App\Entity\GameDataStadium;
use App\Entity\Stades;
use App\Entity\Teams;
use Doctrine\ORM\EntityManagerInterface;

class StadeService
{
    private $doctrineEntityManager;

    public function __construct(EntityManagerInterface $doctrineEntityManager)
    {
        $this->doctrineEntityManager = $doctrineEntityManager;
    }

    public function renommerStade(Teams $equipe, $nouveauNomStade)
    {
        /** @var Stades $stade */
        $stade = $equipe->getFStades();

        if ($stade->getId() == 0) {
            $stade = new Stades();
            $typeStade = $this->doctrineEntityManager->getRepository(GameDataStadium::class)->findOneBy(['id'=>0]);

            $stade->setFTypeStade($typeStade);
            $stade->setTotalPayement(0);
            $this->doctrineEntityManager->persist($stade);
            $equipe->setFStades($stade);
        }

        /** @var Stades $stade */
        $stade->setNom($nouveauNomStade);

        $this->doctrineEntityManager->persist($stade);
        $this->doctrineEntityManager->persist($equipe);

        $this->doctrineEntityManager->flush();
    }

    /**
     * @param Teams $equipe
     * @param string $nomDuStade
     * @param GameDataStadium $typeStade
     */
    public function construireStade(Teams $equipe, $nomDuStade, $typeStade)
    {
        $stade = $equipe->getFStades();

        $stade->setNom($nomDuStade);

        $stade->setTotalPayement(0);

        $stade->setFTypeStade($typeStade);

        $this->doctrineEntityManager->persist($stade);
        $this->doctrineEntityManager->persist($equipe);

        $this->doctrineEntityManager->flush();
    }
}
