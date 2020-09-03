<?php

namespace App\Tests\src\Service\ClassementService;


use App\Entity\Coaches;
use App\Entity\Matches;
use App\Entity\Teams;
use App\Service\ClassementService;
use App\Service\EquipeService;
use App\Service\MatchDataService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class confrontationTousLesCoachesTest extends KernelTestCase
{
    /**
     * @test
     */
    public function un_tableau_de_confrontation_est_retourne(): void
    {
        $coachTest0 = $this->createMock(Coaches::class);
        $coachTest0->method('getName')->willReturn('coach 0');
        $coachTest0->method('getCoachId')->willReturn(0);

        $coachTest1 = $this->createMock(Coaches::class);
        $coachTest1->method('getName')->willReturn('coach 1');
        $coachTest1->method('getCoachId')->willReturn(1);

        $coachTest2 = $this->createMock(Coaches::class);
        $coachTest2->method('getName')->willReturn('coach 2');
        $coachTest2->method('getCoachId')->willReturn(2);

        $coachTest3 = $this->createMock(Coaches::class);
        $coachTest3->method('getName')->willReturn('coach 3');
        $coachTest3->method('getCoachId')->willReturn(3);

        $equipeTest0 = new Teams();
        $equipeTest0->setOwnedByCoach($coachTest0);
        $equipeTest1 = new Teams();
        $equipeTest1->setOwnedByCoach($coachTest1);
        $equipeTest2 = new Teams();
        $equipeTest2->setOwnedByCoach($coachTest2);
        $equipeTest3 = new Teams();
        $equipeTest3->setOwnedByCoach($coachTest3);

        $match0 = new Matches();
        $match0->setTeam1($equipeTest0);
        $match0->setTeam2($equipeTest1);
        $match1 = new Matches();
        $match1->setTeam1($equipeTest1);
        $match1->setTeam2($equipeTest0);
        $match2 = new Matches();
        $match2->setTeam1($equipeTest0);
        $match2->setTeam2($equipeTest2);
        $match3 = new Matches();
        $match3->setTeam1($equipeTest2);
        $match3->setTeam2($equipeTest0);
        $match4 = new Matches();
        $match4->setTeam1($equipeTest0);
        $match4->setTeam2($equipeTest3);
        $match5 = new Matches();
        $match5->setTeam1($equipeTest3);
        $match5->setTeam2($equipeTest0);

        $coachesRepoMock = $this->getMockBuilder(Coaches::class)->setMethods(['tousLesAutresCoaches'])->getMock();
        $coachesRepoMock->method('tousLesAutresCoaches')->willReturn([$coachTest1, $coachTest2, $coachTest3]);

        $matchesRepoMock = $this->getMockBuilder(Matches::class)->setMethods(['tousLesMatchsDeDeuxCoach'])->getMock();
        $matchesRepoMock->method('tousLesMatchsDeDeuxCoach')->willReturnOnConsecutiveCalls(
            [$match0, $match1],
            [$match2, $match3],
            [$match4, $match5]
        );

        $objectManager = $this->createMock(EntityManagerInterface::class);
        $objectManager->method('getRepository')->will(
            $this->returnCallback(
                function ($entityName) use ($coachesRepoMock, $matchesRepoMock) {
                    if ($entityName === 'App\Entity\Coaches') {
                        return $coachesRepoMock;
                    }

                    if ($entityName === 'App\Entity\Matches') {
                        return $matchesRepoMock;
                    }

                    return true;
                }
            )
        );

        $classementServiceTest = new ClassementService(
            $objectManager,
            $this->createMock(EquipeService::class),
            $this->createMock(MatchDataService::class)
        );

        $equipeServiceMock = $this->createMock(EquipeService::class);
        $equipeServiceMock->method('resultatDuMatch')->willReturnOnConsecutiveCalls(
            ['win' => 1, 'draw' => 0, 'loss' => 0],
            ['win' => 0, 'draw' => 0, 'loss' => 1],
            ['win' => 1, 'draw' => 0, 'loss' => 0],
            ['win' => 1, 'draw' => 0, 'loss' => 0],
            ['win' => 0, 'draw' => 0, 'loss' => 1],
            ['win' => 0, 'draw' => 0, 'loss' => 1]
        );

        $tableauAttendu = [
            'coach 1' => [
                '50%', 1, 0, 1, 1
            ],
            'coach 2' => [
                '100%', 2, 0, 0, 2
            ],
            'coach 3' => [
                '0%', 0, 0, 2, 3
            ]
        ];

        $this->assertEquals(
            $tableauAttendu,
            $classementServiceTest->confrontationTousLesCoaches($coachTest0, $equipeServiceMock)
        );
    }


    /**
     * @test
     */
    public function il_n_y_a_pas_de_matchs_mais_des_equipes_et_coachs(): void
    {
        $coachTest0 = new Coaches();
        $coachTest0->setName('coach 0');
        $coachTest1 = new Coaches();
        $coachTest1->setName('coach 1');
        $coachTest2 = new Coaches();
        $coachTest2->setName('coach 2');
        $coachTest3 = new Coaches();
        $coachTest3->setName('coach 3');

        $equipeTest0 = new Teams();
        $equipeTest0->setOwnedByCoach($coachTest0);
        $equipeTest1 = new Teams();
        $equipeTest1->setOwnedByCoach($coachTest1);
        $equipeTest2 = new Teams();
        $equipeTest2->setOwnedByCoach($coachTest2);
        $equipeTest3 = new Teams();
        $equipeTest3->setOwnedByCoach($coachTest3);


        $coachesRepoMock = $this->getMockBuilder(Coaches::class)->setMethods(['tousLesAutresCoaches'])->getMock();
        $coachesRepoMock->method('tousLesAutresCoaches')->willReturn([$coachTest1, $coachTest2, $coachTest3]);

        $matchesRepoMock = $this->getMockBuilder(Matches::class)->setMethods(['tousLesMatchsDeDeuxCoach'])->getMock();
        $matchesRepoMock->method('tousLesMatchsDeDeuxCoach')->willReturnOnConsecutiveCalls([]);

        $objectManager = $this->createMock(EntityManagerInterface::class);
        $objectManager->method('getRepository')->will(
            $this->returnCallback(
                function ($entityName) use ($coachesRepoMock, $matchesRepoMock) {
                    if ($entityName === 'App\Entity\Coaches') {
                        return $coachesRepoMock;
                    }

                    if ($entityName === 'App\Entity\Matches') {
                        return $matchesRepoMock;
                    }

                    return true;
                }
            )
        );

        $classementServiceTest = new ClassementService(
            $objectManager,
            $this->createMock(EquipeService::class),
            $this->createMock(MatchDataService::class)
        );

        $equipeServiceMock = $this->createMock(EquipeService::class);

        $tableauAttendu = [
            'coach 1' => ['N/A'],
            'coach 2' => ['N/A'],
            'coach 3' => ['N/A'],
        ];

        $this->assertEquals(
            $tableauAttendu,
            $classementServiceTest->confrontationTousLesCoaches($coachTest0, $equipeServiceMock)
        );
    }

    /**
     * @test
     */
    public function il_n_y_a_pas_de_donnees(): void
    {
        $coachTest0 = new Coaches();
        $coachTest0->setName('coach 0');

        $equipeTest0 = new Teams();
        $equipeTest0->setOwnedByCoach($coachTest0);

        $coachesRepoMock = $this->getMockBuilder(Coaches::class)->setMethods(['tousLesAutresCoaches'])->getMock();
        $coachesRepoMock->method('tousLesAutresCoaches')->willReturn([]);

        $objectManager = $this->createMock(EntityManagerInterface::class);
        $objectManager->method('getRepository')->willReturn($coachesRepoMock);

        $classementServiceTest = new ClassementService(
            $objectManager,
            $this->createMock(EquipeService::class),
            $this->createMock(MatchDataService::class)
        );

        $equipeServiceMock = $this->createMock(EquipeService::class, $this->createMock(EquipeService::class));

        $tableauAttendu = [];

        $this->assertEquals(
            $tableauAttendu,
            $classementServiceTest->confrontationTousLesCoaches($coachTest0, $equipeServiceMock)
        );
    }

    /**
     * @test
     */
    public function une_paire_de_coach_ne_se_sont_pas_rencontre(): void
    {
        $coachTest0 = $this->createMock(Coaches::class);
        $coachTest0->method('getName')->willReturn('coach 0');
        $coachTest0->method('getCoachId')->willReturn(0);

        $coachTest1 = $this->createMock(Coaches::class);
        $coachTest1->method('getName')->willReturn('coach 1');
        $coachTest1->method('getCoachId')->willReturn(1);

        $coachTest2 = $this->createMock(Coaches::class);
        $coachTest2->method('getName')->willReturn('coach 2');
        $coachTest2->method('getCoachId')->willReturn(2);

        $coachTest3 = $this->createMock(Coaches::class);
        $coachTest3->method('getName')->willReturn('coach 3');
        $coachTest3->method('getCoachId')->willReturn(3);

        $equipeTest0 = new Teams();
        $equipeTest0->setOwnedByCoach($coachTest0);
        $equipeTest1 = new Teams();
        $equipeTest1->setOwnedByCoach($coachTest1);
        $equipeTest2 = new Teams();
        $equipeTest2->setOwnedByCoach($coachTest2);
        $equipeTest3 = new Teams();
        $equipeTest3->setOwnedByCoach($coachTest3);


        $match0 = new Matches();
        $match0->setTeam1($equipeTest0);
        $match0->setTeam2($equipeTest1);
        $match1 = new Matches();
        $match1->setTeam1($equipeTest1);
        $match1->setTeam2($equipeTest0);
        $match4 = new Matches();
        $match4->setTeam1($equipeTest0);
        $match4->setTeam2($equipeTest3);
        $match5 = new Matches();
        $match5->setTeam1($equipeTest3);
        $match5->setTeam2($equipeTest0);


        $coachesRepoMock = $this->getMockBuilder(Coaches::class)->setMethods(['tousLesAutresCoaches'])->getMock();
        $coachesRepoMock->method('tousLesAutresCoaches')->willReturn([$coachTest1, $coachTest2, $coachTest3]);

        $matchesRepoMock = $this->getMockBuilder(Matches::class)->setMethods(['tousLesMatchsDeDeuxCoach'])->getMock();
        $matchesRepoMock->method('tousLesMatchsDeDeuxCoach')->willReturnOnConsecutiveCalls(
            [$match0, $match1],
            [],
            [$match4, $match5]
        );

        $objectManager = $this->createMock(EntityManagerInterface::class);
        $objectManager->method('getRepository')->will(
            $this->returnCallback(
                function ($entityName) use ($coachesRepoMock, $matchesRepoMock) {
                    if ($entityName === 'App\Entity\Coaches') {
                        return $coachesRepoMock;
                    }

                    if ($entityName === 'App\Entity\Matches') {
                        return $matchesRepoMock;
                    }

                    return true;
                }
            )
        );

        $classementServiceTest = new ClassementService(
            $objectManager,
            $this->createMock(EquipeService::class),
            $this->createMock(MatchDataService::class)
        );

        $equipeServiceMock = $this->createMock(EquipeService::class);
        $equipeServiceMock->method('resultatDuMatch')->willReturnOnConsecutiveCalls(
            ['win' => 1, 'draw' => 0, 'loss' => 0],
            ['win' => 0, 'draw' => 0, 'loss' => 1],
            ['win' => 0, 'draw' => 0, 'loss' => 1],
            ['win' => 0, 'draw' => 0, 'loss' => 1]
        );

        $tableauAttendu = [
            'coach 1' => ['50%', 1, 0, 1,1],
            'coach 2' => ['N/A'],
            'coach 3' => ['0%',0,0,2,3]
        ];

        $this->assertEquals(
            $tableauAttendu,
            $classementServiceTest->confrontationTousLesCoaches($coachTest0, $equipeServiceMock)
        );
    }


    /**
     * @test
     */
    public function les_coaches_ont_plusieurs_equipes(): void
    {
        $coachTest0 = $this->createMock(Coaches::class);
        $coachTest0->method('getName')->willReturn('coach 0');
        $coachTest0->method('getCoachId')->willReturn(0);

        $coachTest1 = $this->createMock(Coaches::class);
        $coachTest1->method('getName')->willReturn('coach 1');
        $coachTest1->method('getCoachId')->willReturn(1);

        $coachTest2 = $this->createMock(Coaches::class);
        $coachTest2->method('getName')->willReturn('coach 2');
        $coachTest2->method('getCoachId')->willReturn(2);

        $coachTest3 = $this->createMock(Coaches::class);
        $coachTest3->method('getName')->willReturn('coach 3');
        $coachTest3->method('getCoachId')->willReturn(3);

        $equipeTest0a = new Teams();
        $equipeTest0a->setOwnedByCoach($coachTest0);
        $equipeTest0b = new Teams();
        $equipeTest0b->setOwnedByCoach($coachTest0);
        $equipeTest1a = new Teams();
        $equipeTest1a->setOwnedByCoach($coachTest1);
        $equipeTest1b = new Teams();
        $equipeTest1b->setOwnedByCoach($coachTest1);
        $equipeTest2a = new Teams();
        $equipeTest2a->setOwnedByCoach($coachTest2);
        $equipeTest2b = new Teams();
        $equipeTest2b->setOwnedByCoach($coachTest2);
        $equipeTest3a = new Teams();
        $equipeTest3a->setOwnedByCoach($coachTest3);
        $equipeTest3b = new Teams();
        $equipeTest3b->setOwnedByCoach($coachTest3);

        $match0 = new Matches();
        $match0->setTeam1($equipeTest0a);
        $match0->setTeam2($equipeTest1a);
        $match1 = new Matches();
        $match1->setTeam1($equipeTest1b);
        $match1->setTeam2($equipeTest0b);
        $match2 = new Matches();
        $match2->setTeam1($equipeTest0a);
        $match2->setTeam2($equipeTest2a);
        $match3 = new Matches();
        $match3->setTeam1($equipeTest2b);
        $match3->setTeam2($equipeTest0b);
        $match4 = new Matches();
        $match4->setTeam1($equipeTest0a);
        $match4->setTeam2($equipeTest3a);
        $match5 = new Matches();
        $match5->setTeam1($equipeTest3b);
        $match5->setTeam2($equipeTest0b);

        $coachesRepoMock = $this->getMockBuilder(Coaches::class)->setMethods(['tousLesAutresCoaches'])->getMock();
        $coachesRepoMock->method('tousLesAutresCoaches')->willReturn([$coachTest1, $coachTest2, $coachTest3]);

        $matchesRepoMock = $this->getMockBuilder(Matches::class)->setMethods(['tousLesMatchsDeDeuxCoach'])->getMock();
        $matchesRepoMock->method('tousLesMatchsDeDeuxCoach')->willReturnOnConsecutiveCalls(
            [$match0, $match1],
            [$match2, $match3],
            [$match4, $match5]
        );

        $objectManager = $this->createMock(EntityManagerInterface::class);
        $objectManager->method('getRepository')->will(
            $this->returnCallback(
                function ($entityName) use ($coachesRepoMock, $matchesRepoMock) {
                    if ($entityName === 'App\Entity\Coaches') {
                        return $coachesRepoMock;
                    }

                    if ($entityName === 'App\Entity\Matches') {
                        return $matchesRepoMock;
                    }

                    return true;
                }
            )
        );

        $classementServiceTest = new ClassementService(
            $objectManager,
            $this->createMock(EquipeService::class),
            $this->createMock(MatchDataService::class)
        );

        $equipeServiceMock = $this->createMock(EquipeService::class);
        $equipeServiceMock->method('resultatDuMatch')->willReturnOnConsecutiveCalls(
            ['win' => 1, 'draw' => 0, 'loss' => 0],
            ['win' => 0, 'draw' => 0, 'loss' => 1],
            ['win' => 1, 'draw' => 0, 'loss' => 0],
            ['win' => 1, 'draw' => 0, 'loss' => 0],
            ['win' => 0, 'draw' => 0, 'loss' => 1],
            ['win' => 0, 'draw' => 0, 'loss' => 1]
        );

        $tableauAttendu = [
            'coach 1' => [
                '50%', 1, 0, 1, 1
            ],
            'coach 2' => [
                '100%', 2, 0, 0, 2
            ],
            'coach 3' => [
                '0%', 0, 0, 2, 3
            ]
        ];

        $this->assertEquals(
            $tableauAttendu,
            $classementServiceTest->confrontationTousLesCoaches($coachTest0, $equipeServiceMock)
        );
    }
}