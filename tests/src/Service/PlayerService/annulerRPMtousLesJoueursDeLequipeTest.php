<?php

namespace App\Tests\src\Service\PlayerService;

use App\Entity\Players;
use App\Entity\Teams;
use App\Service\MatchDataService;
use App\Tests\src\TestServiceFactory\PlayerServiceTestFactory;
use Doctrine\ORM\EntityManagerInterface;
use PHPUnit\Framework\TestCase;

class annulerRPMtousLesJoueursDeLequipeTest extends TestCase
{
    /**
     * @test
     */
    public function les_rpm_de_l_equipe_sont_bien_supprime(): void
    {
        $equipeMock = $this->createMock(Teams::class);

        $joueur0 = new Players();
        $joueur0->setInjRpm(1);
        $joueur1 = new Players();
        $joueur2 = new Players();
        $joueur3 = new Players();
        $joueur3->setInjRpm(1);
        $joueur4 = new Players();

        $playerRepoMock = $this->getMockBuilder(Players::class)
            ->addMethods(['listeDesJoueursPourlEquipe'])
            ->getMock();
        $playerRepoMock->method('listeDesJoueursPourlEquipe')->willReturn(
            [$joueur0, $joueur1, $joueur2, $joueur3, $joueur4]
        );

        $objectManager = $this->createMock(EntityManagerInterface::class);
        $objectManager->method('getRepository')->willReturn($playerRepoMock);

        $matchDataService = new MatchDataService($objectManager);

        $playerService = (new PlayerServiceTestFactory)->getInstance(
            $objectManager,
            $matchDataService
        );

        $playerService->annulerRPMtousLesJoueursDeLequipe($equipeMock);
        $this->assertEquals(0, $joueur0->getInjRpm());
        $this->assertEquals(0, $joueur3->getInjRpm());
    }
}