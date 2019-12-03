<?php

namespace App\Tests\src\Service\ClassementService;


use App\Entity\Coaches;
use App\Entity\Matches;
use App\Entity\Teams;
use App\Service\ClassementService;
use App\Service\EquipeService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class genereConfrontationTousLesCoachesTest extends KernelTestCase
{
    /**
     * @test
     */
    public function le_resultat_de_tous_les_coaches_est_bien_retournee()
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


        $coachesRepoMock = $this->getMockBuilder(Coaches::class)->setMethods(
            ['tousLesAutresCoaches', 'findAll']
        )->getMock();
        $coachesRepoMock->method('tousLesAutresCoaches')->willReturnOnConsecutiveCalls(
            [$coachTest1, $coachTest2, $coachTest3],
            [$coachTest0, $coachTest2, $coachTest3],
            [$coachTest0, $coachTest1, $coachTest3],
            [$coachTest0, $coachTest1, $coachTest2]
        );
        $coachesRepoMock->method('findAll')->willReturn([$coachTest0, $coachTest1, $coachTest2, $coachTest3]);

        $matchesRepoMock = $this->getMockBuilder(Matches::class)->setMethods(['tousLesMatchsDeDeuxCoach'])->getMock();
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

        $classementServiceTest = new ClassementService($objectManager);

        $equipeServiceMock = $this->createMock(EquipeService::class);
        $equipeServiceMock->method('resultatDuMatch')->willReturnOnConsecutiveCalls(
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
                ],
                'coach 2' => [
                    '100%',
                    2,
                    0,
                    0,
                ],
                'coach 3' => [
                    '0%',
                    0,
                    0,
                    2,
                ],
            ],
            'coach 1' => [
                'coach 0' => [
                    '50%',
                    1,
                    0,
                    1,
                ],
                'coach 2' => [
                    '100%',
                    1,
                    1,
                    0,
                ],
                'coach 3' => [
                    '50%',
                    1,
                    0,
                    1,
                ],
            ],
            'coach 2' => [
                'coach 0' => [
                    '0%',
                    0,
                    0,
                    2,
                ],
                'coach 1' => [
                    '0%',
                    0,
                    1,
                    1,
                ],
                'coach 3' => [
                    '0%',
                    0,
                    1,
                    1,
                ],
            ],
            'coach 3' => [
                'coach 0' => [
                    '100%',
                    2,
                    0,
                    0,
                ],
                'coach 1' => [
                    '50%',
                    1,
                    0,
                    1,
                ],
                'coach 2' => [
                    '100%',
                    1,
                    1,
                    0,
                ],
            ],
        ];

        $this->assertEquals(
            $tableauAttendu,
            $classementServiceTest->genereConfrontationTousLesCoaches($equipeServiceMock)
        );
    }
}