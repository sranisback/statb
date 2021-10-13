<?php

namespace App\Service;

use App\Entity\GameDataStadium;
use App\Entity\Teams;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\InputBag;
use Symfony\Component\HttpFoundation\Request;

class StadeService
{
    /**
     * @var EntityManagerInterface
     */
    private EntityManagerInterface $doctrineEntityManager;

    public function __construct(EntityManagerInterface $doctrineEntityManager)
    {
        $this->doctrineEntityManager = $doctrineEntityManager;
    }

    /**
     * @param Teams $equipe
     * @param string $nomDuStade
     * @param GameDataStadium $typeStade
     * @param int $niveauAatteindre
     * @return bool
     */
    public function construireStade(
        Teams $equipe,
        string $nomDuStade,
        \App\Entity\GameDataStadium $typeStade,
        int $niveauAatteindre
    ): bool {
        $tableCoutStade = [
            0 => 0,
            1 => 150_000,
            2 => 250_000,
            3 => 500_000,
            4 => 750_000,
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

    /**
     * @param Teams $equipe
     * @param string $nomDuStade
     * @param GameDataStadium $typeStade
     * @return bool
     */
    public function emenagerResidence(Teams $equipe, string $nomDuStade, \App\Entity\GameDataStadium $typeStade): bool
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

    public function creerStade(Request $request, Teams $equipe): bool
    {
        /* @var InputBag $form */
        $form = $request->request->get('creer_stade');

        /** @var GameDataStadium $typeStade */
        $typeStade = $this
            ->doctrineEntityManager
            ->getRepository(GameDataStadium::class)
            ->findOneBy(['id' => $form['fTypeStade']]); /* @phpstan-ignore-line */

        if ($form['niveau'] === '5') {  /* @phpstan-ignore-line */
            $this->emenagerResidence(
                $equipe,
                $form['nom'],  /* @phpstan-ignore-line */
                $typeStade
            );

            return true;
        }

        $this->construireStade(
            $equipe,
            $form['nom'],  /* @phpstan-ignore-line */
            $typeStade,
            /* @phpstan-ignore-next-line */
            $form['niveau']
        );

        return true;
    }
}
