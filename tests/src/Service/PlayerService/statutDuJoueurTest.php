<?php

namespace App\Tests\src\Service\PlayerService;

use App\Entity\Players;
use App\Service\EquipeService;
use App\Service\MatchDataService;
use App\Service\PlayerService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class statutDuJoueurTest extends KernelTestCase
{
    /**
     * @test
     */
    public function le_statut_est_bien_retourne()
    {
        $joueur = new Players();
        $joueur->setStatus(9);

        $objectManager = $this->createMock(EntityManagerInterface::class);

        $matchDataService = new MatchDataService($objectManager);

        $playerService = new PlayerService(
            $objectManager,
            $this->createMock(EquipeService::class),
            $matchDataService
        );

        $this->assertEquals('XP', $playerService->statutDuJoueur($joueur));
    }
}