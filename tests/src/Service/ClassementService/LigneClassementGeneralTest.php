<?php


namespace App\Tests\src\Service\ClassementService;

use App\Entity\Matches;
use App\Entity\Penalite;
use App\Entity\Teams;
use App\Service\ClassementService;
use App\Service\EquipeService;
use App\Service\MatchDataService;
use App\Service\SettingsService;
use Doctrine\ORM\EntityManagerInterface;
use PHPUnit\Framework\TestCase;

class LigneClassementGeneralTest extends TestCase
{
    private ClassementService $classementService;

    private $objectManager;

    private $equipeService;

    private $matchDateService;

    private $settingService;

    public function setUp(): void
    {
        parent::setUp();

        $this->objectManager = $this->createMock(EntityManagerInterface::class);

        $this->equipeService = $this->createMock(EquipeService::class);

        $this->matchDateService = $this->createMock(MatchDataService::class);

        $this->settingService = $this->createMock(SettingsService::class);

        $this->classementService = new ClassementService(
            $this->objectManager,
            $this->equipeService,
            $this->matchDateService,
            $this->settingService
        );
    }

    /**
     * @test
     */
    public function une_ligne_classement_gen_est_generee()
    {
        $equipeMock0 = $this->createMock(Teams::class);

        $matchMock0 = new Matches();
        $matchMock0->setTeam1($equipeMock0);
        $matchMock0->setTeam2($this->createMock(Teams::class));
        $matchMock0->setTeam1Score(3);

        $matchMock1 = new Matches();
        $matchMock1->setTeam1($equipeMock0);
        $matchMock1->setTeam2($this->createMock(Teams::class));

        $matchMock2 = new Matches();
        $matchMock2->setTeam1($equipeMock0);
        $matchMock2->setTeam2($this->createMock(Teams::class));

        $matchRepoMock = $this->getMockBuilder(Matches::class)
            ->addMethods(['listeDesMatchs'])
            ->getMock();

        $matchRepoMock->method('listeDesMatchs')->willReturnOnConsecutiveCalls(
            [],
            [
                $matchMock0,
                $matchMock1,
                $matchMock2
            ],
            [
                $matchMock0,
                $matchMock1,
                $matchMock2
            ]
        );

        $penaliteRepoMock = $this->getMockBuilder(Penalite::class)
            ->addMethods(['penaliteDuneEquipe'])
            ->getMock();
        $penaliteRepoMock->method('penaliteDuneEquipe')->willReturn(2);

        $this->objectManager->method('getRepository')->will(
            $this->returnCallback(
                function ($entityName) use ($penaliteRepoMock, $matchRepoMock) {
                    if ($entityName === Penalite::class) {
                        return $penaliteRepoMock;
                    }

                    if ($entityName === Matches::class) {
                        return $matchRepoMock;
                    }

                    return true;
                }
            )
        );

        $this->settingService->method('anneeCourante')->willReturn(7);

        $this->equipeService->method('resultatsDelEquipe')->willReturn(['win' => 1, 'draw' => 1, 'loss' => 1]);
        $this->equipeService->method('resultatDuMatch')->willReturnOnConsecutiveCalls(
            ['win' => 1, 'loss' => 0, 'draw' => 0],
            ['win' => 1, 'loss' => 0, 'draw' => 0],
            ['win' => 1, 'loss' => 0, 'draw' => 0],
            ['win' => 1, 'loss' => 0, 'draw' => 0],
            ['win' => 1, 'loss' => 0, 'draw' => 0],
            ['win' => 0, 'loss' => 1, 'draw' => 0],
            ['win' => 0, 'loss' => 1, 'draw' => 0],
            ['win' => 0, 'loss' => 1, 'draw' => 0],
            ['win' => 0, 'loss' => 1, 'draw' => 0],
            ['win' => 0, 'loss' => 1, 'draw' => 0],
            ['win' => 0, 'loss' => 0, 'draw' => 1],
            ['win' => 0, 'loss' => 0, 'draw' => 1],
            ['win' => 0, 'loss' => 0, 'draw' => 1],
            ['win' => 0, 'loss' => 0, 'draw' => 1],
            ['win' => 0, 'loss' => 0, 'draw' => 1]
        );


        $this->matchDateService->method('nombreDeSortiesDunMatch')->willReturnOnConsecutiveCalls(2,5,1,2,0,5,0,1,0);

        $this->assertEquals(
            [
                'gagne' => 1,
                'nul' => 1,
                'perdu' => 1,
                'pts' => 8,
                'bonus' => 2,
                'equipe' => $equipeMock0,
                'tdMis' => 3,
                'tdPris' => 0,
                'sortiesPour' => 8,
                'sortiesContre' => 0,
                'penalite' => 2
            ],
            $this->classementService->ligneClassementGeneral($equipeMock0,[8,3,-3])
        );
    }

    /**
     * @test
     */
    public function les_equipes_sont_a_la_meme_tv_classement_poulpi()
    {
        $equipe1 = new Teams();
        $equipe1->setScore(100);
        $equipe2 = new Teams();

        $match = new Matches();
        $match->setTeam1($equipe1);
        $match->setTeam2($equipe2);
        $match->setTeam1Score(1);
        $match->setTeam2Score(0);

        $matchRepoMock = $this->getMockBuilder(Matches::class)
        ->addMethods(['listeDesMatchs'])
        ->getMock();
        $matchRepoMock->method('listeDesMatchs')->willReturn(
            [$match]
        );

        $penaliteRepoMock = $this->getMockBuilder(Penalite::class)
            ->addMethods(['penaliteDuneEquipe'])
            ->getMock();
        $penaliteRepoMock->method('penaliteDuneEquipe')->willReturn(0);

        $this->objectManager->method('getRepository')->will(
            $this->returnCallback(
                function ($entityName) use ($penaliteRepoMock, $matchRepoMock) {
                    if ($entityName === Penalite::class) {
                        return $penaliteRepoMock;
                    }

                    if ($entityName === Matches::class) {
                        return $matchRepoMock;
                    }

                    return true;
                }
            )
        );

        $this->equipeService->method('resultatsDelEquipe')->willReturn(
            ['win' => 1, 'loss' => 0, 'draw' => 0]
        );
        $this->equipeService->method('calculBonusPoulpi')->willReturn(0);

        $this->settingService->method('anneeCourante')->willReturn(8);

        $this->matchDateService->method('nombreDeSortiesDunMatch')->willReturn(1);

        $this->assertEquals(
            [
                'gagne' => 1,
                'nul' => 0,
                'perdu' => 0,
                'pts' => 100,
                'bonus' => 0,
                'equipe' => $equipe1,
                'tdMis' => 1,
                'tdPris' => 0,
                'sortiesPour' => 1,
                'sortiesContre' => 1,
                'penalite' => 0
            ],
            $this->classementService->ligneClassementGeneral($equipe1,[10,0,-10])
        );
    }
}
