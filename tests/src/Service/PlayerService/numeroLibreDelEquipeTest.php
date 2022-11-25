<?php

namespace App\Tests\src\Service\PlayerService;

use App\Entity\Players;
use App\Entity\Teams;
use App\Service\MatchDataService;
use App\Tests\src\TestServiceFactory\PlayerServiceTestFactory;
use Doctrine\ORM\EntityManagerInterface;
use PHPUnit\Framework\TestCase;

class numeroLibreDelEquipeTest extends TestCase
{
    /**
     * @test
     */
    public function le_numero_manquant_du_joueur_est_bien_renvoye(): void
    {
        $equipeMock = $this->createMock(Teams::class);

        $joueur0 = new Players();
        $joueur0->setNr(1);
        $joueur1 = new Players();
        $joueur1->setNr(2);
        $joueur2 = new Players();
        $joueur2->setNr(4);
        $joueur3 = new Players();
        $joueur3->setNr(5);
        $joueur4 = new Players();
        $joueur4->setNr(6);

        $joueurRepoMock = $this->getMockBuilder(Players::class)
            ->addMethods(['listeDesJoueursPourlEquipe'])
            ->getMock();
        $joueurRepoMock->method('listeDesJoueursPourlEquipe')->willReturn(
            [$joueur0, $joueur1, $joueur2, $joueur3, $joueur4]
        );

        $objectManager = $this->createMock(EntityManagerInterface::class);
        $objectManager->method('getRepository')->willReturn($joueurRepoMock);

        $matchDataService = new MatchDataService($objectManager);

        $playerService = (new PlayerServiceTestFactory())->getInstance(
            $objectManager,
            $matchDataService
        );

        $this->assertEquals(3, $playerService->numeroLibreDelEquipe($equipeMock));
    }
}