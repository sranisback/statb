<?php


namespace App\Tests\src\Service\MatchesService;


use App\Entity\Matches;
use App\Entity\ScoreCalcul;
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

class RecalculeLeScoreTest extends TestCase
{
    /**
     * @test
     */
    public function le_score_est_bien_recalcule()
    {
        $equipe1 = new Teams();
        $equipe1->setScore(500);

        $equipe2 = new Teams();
        $equipe2->setScore(400);

        $equipe3 = new Teams();
        $equipe3->setScore(300);

        $equipe4 = new Teams();
        $equipe4->setScore(200);

        $match1 = new Matches();
        $match1->setTeam1($equipe1);
        $match1->setTeam2($equipe2);
        $match1->setTeam1Score(1);
        $match1->setTeam2Score(0);

        $match2 = new Matches();
        $match2->setTeam1($equipe3);
        $match2->setTeam2($equipe4);
        $match2->setTeam1Score(0);
        $match2->setTeam2Score(0);

        $match3 = new Matches();
        $match3->setTeam1($equipe1);
        $match3->setTeam2($equipe3);
        $match3->setTeam1Score(0);
        $match3->setTeam2Score(2);

        $match4 = new Matches();
        $match4->setTeam1($equipe2);
        $match4->setTeam2($equipe4);
        $match4->setTeam1Score(2);
        $match4->setTeam2Score(2);

        $teamRepoMock = $this->getMockBuilder(Teams::class)
            ->addMethods(['findBy'])
            ->getMock();
        $teamRepoMock->method('findBy')->willReturn([$equipe1, $equipe2, $equipe3, $equipe4]);

        $matchRepoMock = $this->getMockBuilder(Matches::class)
            ->addMethods(['tousLesMatchDuneAnneClassementChrono'])
            ->getMock();

        $matchRepoMock->method('tousLesMatchDuneAnneClassementChrono')->willReturn(
            [$match1, $match2, $match3, $match4]
        );

        $scoreCalculMock = $this->getMockBuilder(ScoreCalcul::class)
            ->addMethods(['findAll'])
            ->getMock();
        $scoreCalculMock->method('findAll')->willReturn([]);

        $objectManager = $this->createMock(EntityManagerInterface::class);
        $objectManager->method('getRepository')->will(
            $this->returnCallback(
                function ($entityName) use ($teamRepoMock, $matchRepoMock, $scoreCalculMock) {
                    if ($entityName === Teams::class) {
                        return $teamRepoMock;
                    }

                    if ($entityName === Matches::class) {
                        return $matchRepoMock;
                    }

                    if ($entityName === ScoreCalcul::class) {
                        return $scoreCalculMock;
                    }

                    return true;
                }
            )
        );

        $settingService = $this->createMock(SettingsService::class);
        $settingService->method('pointsEnCours')->willReturn([10, 0, -10]);
        $settingService->method('anneeCourante')->willReturn(8);

        $equipeService = $this->createMock(EquipeService::class);
        $equipeService->method('calculBonusPourUnMatchPoulpi')->willReturn(
            10,-10, // match 1
            0, 0, // match 2
            -1, 1, // match 3
            1, -1 // match 4
        );
        $equipeService->method('maxScore')->willReturn(
            15,-15,  // match 1
            0,0, // match 2
            -11, 15,// match 3
            1, -1 // match 4
        );
        $equipeService->method('resultatDuMatch')->willReturn(
            ['win'=> 1, 'loss'=> 0, 'draw'=> 0], // match 1
            ['win'=> 0, 'loss'=> 1, 'draw'=> 0],
            ['win'=> 0, 'loss'=> 0, 'draw'=> 1], // match 2
            ['win'=> 0, 'loss'=> 0, 'draw'=> 1],
            ['win'=> 0, 'loss'=> 1, 'draw'=> 0], // match 3
            ['win'=> 1, 'loss'=> 0, 'draw'=> 0],
            ['win'=> 0, 'loss'=> 0, 'draw'=> 1], // match 4
            ['win'=> 0, 'loss'=> 0, 'draw'=> 1],
        );


        $matchService = new MatchesService(
            $objectManager,
            $equipeService,
            $this->createMock(PlayerService::class),
            $settingService,
            $this->createMock(DefisService::class),
            $this->createMock(InfosService::class),
            $this->createMock(EquipeGestionService::class)
        );

        $matchService->recalculLeScore();

        $this->assertEquals(104, $equipe1->getScore());
        $this->assertEquals(86, $equipe2->getScore());
        $this->assertEquals(115, $equipe3->getScore());
        $this->assertEquals(99, $equipe4->getScore());
        $this->assertEquals(100, $match1->getScoreClassementTeam1());
        $this->assertEquals(100, $match1->getScoreClassementTeam2());
        $this->assertEquals(100, $match2->getScoreClassementTeam1());
        $this->assertEquals(100, $match2->getScoreClassementTeam2());
        $this->assertEquals(115, $match3->getScoreClassementTeam1());
        $this->assertEquals(100, $match3->getScoreClassementTeam2());
        $this->assertEquals(85, $match4->getScoreClassementTeam1());
        $this->assertEquals(100, $match4->getScoreClassementTeam2());
    }

}