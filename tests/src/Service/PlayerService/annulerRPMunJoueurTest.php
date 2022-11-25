<?php

namespace App\Tests\src\Service\PlayerService;

use App\Entity\Players;
use App\Service\MatchDataService;
use App\Tests\src\TestServiceFactory\PlayerServiceTestFactory;
use Doctrine\ORM\EntityManagerInterface;
use PHPUnit\Framework\TestCase;

class annulerRPMunJoueurTest extends TestCase
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

        $playerService = (new PlayerServiceTestFactory)->getInstance(
            $objectManager,
            $matchDataService
        );

        $playerService->annulerRPMunJoueur($joueur);

        $this->assertEquals(0,$joueur->getInjRpm());
    }
}