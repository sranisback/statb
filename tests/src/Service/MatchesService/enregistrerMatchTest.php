<?php


namespace App\Tests\src\Service\MatchesService;


use App\Entity\GameDataStadium;
use App\Entity\Matches;
use App\Entity\Meteo;
use App\Entity\Stades;
use App\Entity\Teams;
use App\Service\DefisService;
use App\Service\EquipeGestionService;
use App\Service\EquipeService;
use App\Service\InfosService;
use App\Service\MatchesService;
use App\Service\PlayerService;
use App\Service\SettingsService;
use Doctrine\ORM\EntityManagerInterface;
use PHPUnit\Framework\TestCase;
use ReflectionClass;

class enregistrerMatchTest extends TestCase
{
    /**
     * @test
     */
    public function le_match_est_bien_enregistre_sans_joueurs() {

        $typeStade = new GameDataStadium();

        $stade = new Stades();
        $stade->setFTypeStade($typeStade);

        $equipe1 = new Teams();
        $equipe1->setName('1');
        $equipe1->setFStades($stade);
        $equipe1->setScore(50);

        $equipe2 = new Teams();
        $equipe2->setName('2');
        $equipe2->setScore(100);

        $teamRepoMock = $this->getMockBuilder(Teams::class)
            ->addMethods(['findOneBy'])
            ->getMock();
        $teamRepoMock->method('findOneBy')->willReturn($equipe1, $equipe2);

        $gameDataStadiumRepo = $this->getMockBuilder(GameDataStadium::class)
            ->addMethods(['findOneBy'])
            ->getMock();
        $gameDataStadiumRepo->method('findOneBy')->willReturn($typeStade);

        $meteo = new Meteo();

        $meteoRepo = $this->getMockBuilder(Meteo::class)
            ->addMethods(['findOneBy'])
            ->getMock();
        $meteoRepo->method('findOneBy')->willReturn($meteo);

        $objectManager = $this->createMock(EntityManagerInterface::class);
        $objectManager->method('getRepository')->will(
            $this->returnCallback(
                function ($entityName) use ($teamRepoMock, $gameDataStadiumRepo, $meteoRepo) {
                    if ($entityName === Teams::class) {
                        return $teamRepoMock;
                    }

                    if ($entityName === GameDataStadium::class) {
                        return $gameDataStadiumRepo;
                    }

                    if ($entityName === Meteo::class) {
                        return $meteoRepo;
                    }

                    return true;
                }
            )
        );

        $objectManager->expects($this->exactly(5))->method('persist')->willReturnOnConsecutiveCalls(
            $this->returnCallback(
                function($match) {
                    if ($match instanceof Matches) {
                        $reflection = new ReflectionClass($match);
                        $property = $reflection->getProperty('matchId');
                        $property->setAccessible(true);
                        $property->setValue($match, '1');
                        return $match;
                    }

                    return true;
                }
            ),
            null
        );

        $objectManager->expects($this->exactly(2))->method('refresh')->willReturnOnConsecutiveCalls(
            $this->returnCallback(
                function($equipe) {
                    if ($equipe instanceof Teams) {
                        self::assertEquals(65, $equipe->getScore());
                    }

                    return true;
                }
            ),
            $this->returnCallback(
                function($equipe) {
                    if ($equipe instanceof Teams) {
                        self::assertEquals(90, $equipe->getScore());
                    }

                    return true;
                }
            )
        );

        $playerService = $this->createMock(PlayerService::class);
        $defisService = $this->createMock(DefisService::class);
        $infoService = $this->createMock(InfosService::class);

        $equipeService = $this->createMock(EquipeService::class);
        $equipeService->method('calculBonusPourUnMatchPoulpi')->willReturn(10,-10);
        $equipeService->method('maxScore')->willReturn(15,-10);
        $equipeService->method('resultatDuMatch')->willReturn(
            [
                'win'=> 1,
                'loss'=> 0,
                'draw'=> 0,
            ],
            [
                'win'=> 0,
                'loss'=> 1,
                'draw'=> 0,
            ]
        );

        $settingService = $this->createMock(SettingsService::class);
        $settingService->method('pointsEnCours')->willReturn([10, 0, -10]);

        $equipeGestionService = $this->createMock(EquipeGestionService::class);
        $equipeGestionService->method('tvDelEquipe')->willReturn(1500,1250);

        $matchService = new MatchesService(
            $objectManager,
            $equipeService,
            $playerService,
            $settingService,
            $defisService,
            $infoService,
            $equipeGestionService
        );

        $donneeMatch = [
            'team_1' => 1,
            'team_2' => 2,
            'stade' => 1,
            'stadeAccueil' => 1,
            'meteo' => 1,
            'totalpop' => 10000,
            'varpop_team1' => 1,
            'varpop_team2' => 0,
            'gain1' => 50000,
            'gain2' => 25000,
            'score1' => 1,
            'score2' => 0,
            'depense1' => -10000,
            'depense2' => -5000,
            'player' => []
        ];

        $this->assertEquals(['enregistrement' => 1, 'defis' => null], $matchService->enregistrerMatch($donneeMatch));
    }
}