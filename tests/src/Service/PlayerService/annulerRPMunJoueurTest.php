<?php

namespace App\Tests\src\Service\PlayerService;

use App\Entity\Players;
use App\Service\EquipeService;
use App\Service\MatchDataService;
use App\Service\PlayerService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class annulerRPMunJoueurTest extends KernelTestCase
{
    /**
     * @test
     */
    public function le_rpm_d_un_joueur_est_bien_supprime(): void
    {
        $objectManager = $this->createMock(EntityManagerInterface::class);

        $joueur = new Players();
        $joueur->setInjRpm(1);

        $matchDataService = new MatchDataService($objectManager);

        $playerService = new PlayerService(
            $objectManager,
            $this->createMock(EquipeService::class),
            $matchDataService
        );

        $this->assertEquals(0,$playerService->annulerRPMunJoueur($joueur));
    }
}