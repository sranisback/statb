<?php


namespace App\Tests\src\Service\MatchDataService;

use App\Entity\MatchData;
use App\Entity\Matches;
use App\Entity\Players;
use App\Entity\Teams;
use App\Service\MatchDataService;
use Doctrine\ORM\EntityManagerInterface;
use PHPUnit\Framework\TestCase;

class NombreDeSortiesDunMatchTest extends TestCase
{
    /**
     * @test
     */
    public function le_nombre_est_bien_calcule()
    {
        $equipe = new Teams();

        $player0 = new Players();
        $player0->setOwnedByTeam($equipe);

        $player1 = new Players();
        $player1->setOwnedByTeam($equipe);

        $player2 = new Players();
        $player2->setOwnedByTeam($equipe);

        $matchMock = $this->createMock(Matches::class);
        $matchMock->method('getMatchId')->willReturn(1);

        $matchData0 = new MatchData();
        $matchData0->setBh(1);
        $matchData0->setFMatch($matchMock);
        $matchData0->setFPlayer($player0);

        $matchData1 = new MatchData();
        $matchData1->setKi(1);
        $matchData1->setFMatch($matchMock);
        $matchData1->setFPlayer($player1);

        $matchData2 = new MatchData();
        $matchData2->setAgg(1);
        $matchData2->setFMatch($matchMock);
        $matchData2->setFPlayer($player2);

        $joueurRepoMock = $this->getMockBuilder(Players::class)
            ->setMethods(['listeDesJoueursPourlEquipe'])
            ->getMock();

        $joueurRepoMock->method('listeDesJoueursPourlEquipe')->willReturn(
            [$player0,$player1,$player2]
        );

        $matchDataRepoMock = $this->getMockBuilder(MatchData::class)
        ->setMethods(['findOneBy'])
        ->getMock();

        $matchDataRepoMock->method('findOneBy')->willReturnOnConsecutiveCalls(
            $matchData0,$matchData1,$matchData2
        );

        $objectManager = $this->createMock(EntityManagerInterface::class);
        $objectManager->method('getRepository')->will(
            $this->returnCallback(
                function ($entityName) use ($joueurRepoMock, $matchDataRepoMock) {
                    if ($entityName === 'App\Entity\Players') {
                        return $joueurRepoMock;
                    }
                    if ($entityName === 'App\Entity\MatchData') {
                        return $matchDataRepoMock;
                    }
                    return true;
                }
            )
        );

        $matchDataService = new MatchDataService($objectManager);

        $this->assertEquals(3, $matchDataService->nombreDeSortiesDunMatch($equipe, $matchMock));
    }
}
