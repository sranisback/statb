<?php


namespace App\Tests\src\Service\PlayerService;


use App\Entity\MatchData;
use App\Entity\Matches;
use App\Entity\Players;
use App\Service\EquipeService;
use App\Service\MatchDataService;
use App\Service\PlayerService;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\EntityManager;
use PHPUnit\Framework\TestCase;

class listesDesActionsDunJoueurParMatchesTest extends TestCase
{
    /**
     * @test
     */
    public function une_liste_est_renvoyee()
    {
        $joueurMock = $this->createMock(Players::class);
        $joueurMock->method('getPlayerId')->willReturn(1);
        $joueurMock->method('getType')->willReturn(1);

        $matchDataMock = $this->createMock(MatchData::class);

        $matchMock = $this->createMock(Matches::class);
        $matchMock->method('getMatchId')->willReturn(1);
        $matchMock->method('getBlessuresMatch')->willReturn(new ArrayCollection());

        $matchDataRepoMock = $this->getMockBuilder(MatchData::class)
            ->addMethods(['listeDesMatchsdUnJoueur', 'findBy'])
            ->getMock();
        $matchDataRepoMock->method('listeDesMatchsdUnJoueur')->willReturn([$matchMock]);
        $matchDataRepoMock->method('findBy')->willReturn([$matchDataMock]);

        $objectManager = $this->createMock(EntityManager::class);
        $objectManager->method('getRepository')->will(
            $this->returnCallback(
                function ($entityName) use ($matchDataRepoMock) {
                    if ($entityName === MatchData::class) {
                        return $matchDataRepoMock;
                    }
                    return true;
                }
            )
        );

        $equipeServiceMock = $this->createMock(EquipeService::class);

        $matchDataServiceMock = $this->createMock(MatchDataService::class);
        $matchDataServiceMock->method('lectureLignedUnMatch')->willReturn('MVP: 1, ');

        $playerServiceTest = new PlayerService(
            $objectManager,
            $equipeServiceMock,
            $matchDataServiceMock
        );

        $attendu = [
            0 => [$matchMock],
            1 => [
                0 => [
                    'mId' => 1,
                    'data' => 'MVP: 1'
                ]
            ]
        ];

        $this->assertEquals($attendu, $playerServiceTest->listesDesActionsDunJoueurParMatches($joueurMock));
    }

    /**
     * @test
     */
    public function il_n_y_a_pas_de_donnees()
    {
        $joueurMock = $this->createMock(Players::class);
        $joueurMock->method('getPlayerId')->willReturn(1);
        $joueurMock->method('getType')->willReturn(1);

        $matchDataMock = $this->createMock(MatchData::class);

        $matchDataRepoMock = $this->getMockBuilder(MatchData::class)
            ->addMethods(['listeDesMatchsdUnJoueur', 'findBy'])
            ->getMock();

        $matchDataRepoMock->method('listeDesMatchsdUnJoueur')->willReturn(null);
        $matchDataRepoMock->method('findBy')->willReturn([$matchDataMock]);

        $objectManager = $this->createMock(EntityManager::class);
        $objectManager->method('getRepository')->will(
            $this->returnCallback(
                function ($entityName) use ($matchDataRepoMock) {
                    if ($entityName === MatchData::class) {
                        return $matchDataRepoMock;
                    }
                    return true;
                }
            )
        );

        $equipeServiceMock = $this->createMock(EquipeService::class);

        $matchDataServiceMock = $this->createMock(MatchDataService::class);
        $matchDataServiceMock->method('lectureLignedUnMatch')->willReturn('MVP: 1, ');

        $playerServiceTest = new PlayerService(
            $objectManager,
            $equipeServiceMock,
            $matchDataServiceMock
        );

        $attendu = [
            0 => null,
            1 => [
                0 => [
                    'mId' => 0,
                    'data' => ''
                ]
            ]
        ];

        $this->assertEquals($attendu, $playerServiceTest->listesDesActionsDunJoueurParMatches($joueurMock));
    }
}