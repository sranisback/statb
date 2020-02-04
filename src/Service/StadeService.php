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

    /**
     * @param Teams $equipe
     * @param string $nouveauNomStade
     */
    public function renommerStade(Teams $equipe, string $nouveauNomStade)
    {
        /** @var Stades $stade */
        $stade = $equipe->getFStades();

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
     * @return bool
     */
    public function construireStade(Teams $equipe, $nomDuStade, $typeStade, $niveauAatteindre)
    {
        $tableCoutStade = [
            0 => 0,
            1 => 150000,
            2 => 250000,
            3 => 500000,
            4 => 750000,
        ];

        $stade = $equipe->getFStades();

        if ($niveauAatteindre >= $stade->getNiveau()) {
            $coutApayer = $tableCoutStade[$niveauAatteindre] - $tableCoutStade[$stade->getNiveau()];

            if ($coutApayer <= ($stade->getTotalPayement() + $equipe->getTreasury())) {
                $stade->setTotalPayement($stade->getTotalPayement() - $coutApayer);
                if ($stade->getTotalPayement() < 0) {
                    $equipe->setTreasury($equipe->getTreasury() + $stade->getTotalPayement());
                    $stade->setTotalPayement(0);
                }

                $stade->setFTypeStade($typeStade);
                $stade->setNiveau($niveauAatteindre);
                $stade->setNom($nomDuStade);

                $this->doctrineEntityManager->persist($stade);
                $this->doctrineEntityManager->persist($equipe);
                $this->doctrineEntityManager->flush();

                return true;
            }
        }

        return false;
    }

    public function emenagerResidence (Teams $equipe, $nomDuStade, $typeStade)
    {
        $stade = $equipe->getFStades();

        $stade->setFTypeStade($typeStade);
        $stade->setNiveau(5);
        $stade->setNom($nomDuStade);

        $this->doctrineEntityManager->persist($stade);
        $this->doctrineEntityManager->persist($equipe);
        $this->doctrineEntityManager->flush();

        return true;
    }
}
