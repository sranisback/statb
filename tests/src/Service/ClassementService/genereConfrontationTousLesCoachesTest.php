<?php

namespace App\Tests\src\Service\ClassementService;


use App\Entity\Coaches;
use App\Entity\Matches;
use App\Entity\Teams;
use App\Service\ClassementService;
use App\Service\EquipeService;
use App\Service\MatchDataService;
use App\Service\SettingsService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class genereConfrontationTousLesCoachesTest extends KernelTestCase
{
    private ClassementService $classementService;

    private $objectManager;

    private $equipeService;

    public function setUp(): void
    {
        parent::setUp();

        $this->objectManager = $this->createMock(EntityManagerInterface::class);

        $this->equipeService = $this->createMock(EquipeService::class);

        $this->classementService = new ClassementService(
            $this->objectManager,
            $this->equipeService,
            $this->createMock(MatchDataService::class),
            $this->createMock(SettingsService::class)
        );
    }

    /**
     * @test
     */
    public function le_resultat_de_tous_les_coaches_est_bien_retournee(): void
    {
        $coachTest0 = $this->createMock(Coaches::class);
        $coachTest0->method('getUsername')->willReturn('coach 0');
        $coachTest0->method('getCoachId')->willReturn(0);

        $coachTest1 = $this->createMock(Coaches::class);
        $coachTest1->method('getUsername')->willReturn('coach 1');
        $coachTest1->method('getCoachId')->willReturn(1);

        $coachTest2 = $this->createMock(Coaches::class);
        $coachTest2->method('getUsername')->willReturn('coach 2');
        $coachTest2->method('getCoachId')->willReturn(2);

        $coachTest3 = $this->createMock(Coaches::class);
        $coachTest3->method('getUsername')->willReturn('coach 3');
        $coachTest3->method('getCoachId')->willReturn(3);

        $equipeTest0 = new Teams();
        $equipeTest0->setOwnedByCoach($coachTest0);
        $equipeTest1 = new Teams();
        $equipeTest1->setOwnedByCoach($coachTest1);
        $equipeTest2 = new Teams();
        $equipeTest2->setOwnedByCoach($coachTest2);
        $equipeTest3 = new Teams();
        $equipeTest3->setOwnedByCoach($coachTest3);

        //coach 0 - coach 1
        $match0 = new Matches();
        $match0->setTeam1($equipeTest0);
        $match0->setTeam2($equipeTest1);
        $match1 = new Matches();
        $match1->setTeam1($equipeTest1);
        $match1->setTeam2($equipeTest0);

        //coach 0 - coach 2
        $match2 = new Matches();
        $match2->setTeam1($equipeTest0);
        $match2->setTeam2($equipeTest2);
        $match3 = new Matches();
        $match3->setTeam1($equipeTest2);
        $match3->setTeam2($equipeTest0);

        //coach 0 - coach 3
        $match4 = new Matches();
        $match4->setTeam1($equipeTest0);
        $match4->setTeam2($equipeTest3);
        $match5 = new Matches();
        $match5->setTeam1($equipeTest3);
        $match5->setTeam2($equipeTest0);

        //coach 1 - coach 2
        $match6 = new Matches();
        $match6->setTeam1($equipeTest1);
        $match6->setTeam2($equipeTest2);
        $match7 = new Matches();
        $match7->setTeam1($equipeTest2);
        $match7->setTeam2($equipeTest1);

        //coach 1 - coach 3
        $match8 = new Matches();
        $match8->setTeam1($equipeTest1);
        $match8->setTeam2($equipeTest3);
        $match9 = new Matches();
        $match9->setTeam1($equipeTest3);
        $match9->setTeam2($equipeTest1);

        //coach 2 - coach 3
        $match10 = new Matches();
        $match10->setTeam1($equipeTest2);
        $match10->setTeam2($equipeTest3);
        $match11 = new Matches();
        $match11->setTeam1($equipeTest3);
        $match11->setTeam2($equipeTest2);


        $coachesRepoMock = $this->getMockBuilder(Coaches::class)->addMethods(
            ['tousLesAutresCoaches', 'findAll']
        )->getMock();
        $coachesRepoMock->method('tousLesAutresCoaches')->willReturnOnConsecutiveCalls(
            [$coachTest1, $coachTest2, $coachTest3],
            [$coachTest0, $coachTest2, $coachTest3],
            [$coachTest0, $coachTest1, $coachTest3],
            [$coachTest0, $coachTest1, $coachTest2]
        );
        $coachesRepoMock->method('findAll')->willReturn([$coachTest0, $coachTest1, $coachTest2, $coachTest3]);

        $matchesRepoMock = $this->getMockBuilder(Matches::class)->addMethods(['tousLesMatchsDeDeuxCoach'])->getMock();
        $matchesRepoMock->method('tousLesMatchsDeDeuxCoach')->willReturnOnConsecutiveCalls(
            [$match0, $match1], // 0 - 1
            [$match2, $match3], // 0 - 2
            [$match4, $match5], // 0 - 3
            [$match0, $match1], // 1 - 0
            [$match6, $match7], // 1 - 2
            [$match8, $match9], // 1 - 3
            [$match2, $match3], // 2 - 0
            [$match6, $match7], // 2 - 1
            [$match10, $match11], // 2 - 3
            [$match4, $match5],  // 3 - 0
            [$match8, $match9], // 3 - 1
            [$match10, $match11]// 3 -2
        );

        $this->objectManager->method('getRepository')->will(
            $this->returnCallback(
                function ($entityName) use ($coachesRepoMock, $matchesRepoMock) {
                    if ($entityName === Coaches::class) {
                        return $coachesRepoMock;
                    }

                    if ($entityName === Matches::class) {
                        return $matchesRepoMock;
                    }

                    return true;
                }
            )
        );

        $this->equipeService->method('resultatDuMatch')->willReturnOnConsecutiveCalls(
            ['win' => 1, 'draw' => 0, 'loss' => 0],
            ['win' => 0, 'draw' => 0, 'loss' => 1],
            ['win' => 1, 'draw' => 0, 'loss' => 0],
            ['win' => 1, 'draw' => 0, 'loss' => 0],
            ['win' => 0, 'draw' => 0, 'loss' => 1],
            ['win' => 0, 'draw' => 0, 'loss' => 1],
            ['win' => 1, 'draw' => 0, 'loss' => 0],
            ['win' => 0, 'draw' => 0, 'loss' => 1],
            ['win' => 1, 'draw' => 0, 'loss' => 0],
            ['win' => 0, 'draw' => 1, 'loss' => 0],
            ['win' => 1, 'draw' => 0, 'loss' => 0],
            ['win' => 0, 'draw' => 0, 'loss' => 1],
            ['win' => 0, 'draw' => 0, 'loss' => 1],
            ['win' => 0, 'draw' => 0, 'loss' => 1],
            ['win' => 0, 'draw' => 1, 'loss' => 0],
            ['win' => 0, 'draw' => 0, 'loss' => 1],
            ['win' => 0, 'draw' => 1, 'loss' => 0],
            ['win' => 0, 'draw' => 0, 'loss' => 1],
            ['win' => 1, 'draw' => 0, 'loss' => 0],
            ['win' => 1, 'draw' => 0, 'loss' => 0],
            ['win' => 1, 'draw' => 0, 'loss' => 0],
            ['win' => 0, 'draw' => 0, 'loss' => 1],
            ['win' => 1, 'draw' => 0, 'loss' => 0],
            ['win' => 0, 'draw' => 1, 'loss' => 0]
        );

        $tableauAttendu = [
            'coach 0' => [
                'coach 1' => [
                    '50%',
                    1,
                    0,
                    1,
                    1
                ],
                'coach 2' => [
                    '100%',
                    2,
                    0,
                    0,
                    2
                ],
                'coach 3' => [
                    '0%',
                    0,
                    0,
                    2,
                    3
                ],
            ],
            'coach 1' => [
                'coach 0' => [
                    '50%',
                    1,
                    0,
                    1,
                    0
                ],
                'coach 2' => [
                    '100%',
                    1,
                    1,
                    0,
                    2
                ],
                'coach 3' => [
                    '50%',
                    1,
                    0,
                    1,
                    3
                ],
            ],
            'coach 2' => [
                'coach 0' => [
                    '0%',
                    0,
                    0,
                    2,
                    0
                ],
                'coach 1' => [
                    '0%',
                    0,
                    1,
                    1,
                    1
                ],
                'coach 3' => [
                    '0%',
                    0,
                    1,
                    1,
                    3
                ],
            ],
            'coach 3' => [
                'coach 0' => [
                    '100%',
                    2,
                    0,
                    0,
                    0
                ],
                'coach 1' => [
                    '50%',
                    1,
                    0,
                    1,
                    1
                ],
                'coach 2' => [
                    '100%',
                    1,
                    1,
                    0,
                    2
                ],
            ],
        ];

        $this->assertEquals(
            $tableauAttendu,
            $this->classementService->genereConfrontationTousLesCoaches($this->equipeService)
        );
    }
}