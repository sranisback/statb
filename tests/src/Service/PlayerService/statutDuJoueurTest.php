<?php

namespace App\Tests\src\Service\PlayerService;

use App\Entity\Players;
use App\Service\MatchDataService;
use App\Tests\src\TestServiceFactory\PlayerServiceTestFactory;
use Doctrine\ORM\EntityManagerInterface;
use PHPUnit\Framework\TestCase;

class statutDuJoueurTest extends TestCase
{
    /**
     * @test
     */
    public function le_statut_est_bien_retourne(): void
    {
        $joueur = new Players();
        $joueur->setStatus(9);

        $objectManager = $this->createMock(EntityManagerInterface::class);

        $matchDataService = new MatchDataService($objectManager);

        $playerService = (new PlayerServiceTestFactory)->getInstance(
            $objectManager,
            $matchDataService
        );

        $this->assertEquals('XP', $playerService->statutDuJoueur($joueur));
    }
}