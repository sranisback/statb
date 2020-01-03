<?php


namespace App\Tests\src\Service\ClassementService;


use App\Entity\Matches;
use App\Entity\Teams;
use App\Service\ClassementService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class classementDetailTest extends KernelTestCase
{
    /**
     * @test
     */
    public function classement_genere()
    {
        $equipeMock0 = $this->createMock(Teams::class);
        $equipeMock0->method('getTeamId')->willReturn(0);
        $equipeMock1 = $this->createMock(Teams::class);
        $equipeMock1->method('getTeamId')->willReturn(1);
        $equipeMock2 = $this->createMock(Teams::class);
        $equipeMock2->method('getTeamId')->willReturn(2);

        $matchMock0 = $this->createMock(Matches::class);
        $matchMock0->method('getTeam1')->willReturn($equipeMock0);
        $matchMock0->method('getTeam1Score')->willReturn(2);
        $matchMock0->method('getTeam2Score')->willReturn(2);
        $matchMock1 = $this->createMock(Matches::class);
        $matchMock1->method('getTeam2')->willReturn($equipeMock0);
        $matchMock1->method('getTeam1Score')->willReturn(2);
        $matchMock1->method('getTeam2Score')->willReturn(2);
        $matchMock2 = $this->createMock(Matches::class);
        $matchMock2->method('getTeam1')->willReturn($equipeMock1);
        $matchMock2->method('getTeam1Score')->willReturn(2);
        $matchMock2->method('getTeam2Score')->willReturn(1);
        $matchMock3 = $this->createMock(Matches::class);
        $matchMock3->method('getTeam2')->willReturn($equipeMock1);
        $matchMock3->method('getTeam2Score')->willReturn(2);
        $matchMock4 = $this->createMock(Matches::class);
        $matchMock4->method('getTeam1')->willReturn($equipeMock2);
        $matchMock4->method('getTeam1Score')->willReturn(2);
        $matchMock5 = $this->createMock(Matches::class);
        $matchMock5->method('getTeam2')->willReturn($equipeMock2);
        $matchMock5->method('getTeam2Score')->willReturn(0);

        $matchRepoMock = $this->getMockBuilder(Matches::class)
            ->setMethods(['listeDesMatchs'])
            ->getMock();

        $matchRepoMock->method('listeDesMatchs')->willReturnOnConsecutiveCalls(
            [$matchMock0, $matchMock1],
            [$matchMock2, $matchMock3],
            [$matchMock4, $matchMock5]
        );

        $teamRepoMock = $this->getMockBuilder(Teams::class)
            ->setMethods(['findBy', 'pointsBonus'])
            ->getMock();

        $teamRepoMock->method('findBy')->willReturn(
            [$equipeMock0, $equipeMock1, $equipeMock2]
        );

        $teamRepoMock->method('pointsBonus')->willReturn(
            [
                [
                    'equipeId' => 0,
                    'Bonus' => 5
                ],
                [
                    'equipeId' => 1,
                    'Bonus' => 3
                ],
                [
                    'equipeId' => 2,
                    'Bonus' => 2
                ]
            ]
        );

        $objectManager = $this->createMock(EntityManagerInterface::class);
        $objectManager->method('getRepository')->will(
            $this->returnCallback(
                function ($entityName) use ($teamRepoMock, $matchRepoMock) {
                    if ($entityName === 'App\Entity\Teams') {
                        return $teamRepoMock;
                    }

                    if ($entityName === 'App\Entity\Matches') {
                        return $matchRepoMock;
                    }

                    return true;
                }
            )
        );

        $retour = [
            [
                'equipe' => $equipeMock0,
                'tdMis' => 4,
                'tdPris' => 4,
                'tdAverage' => 0,
                'pts' => 5
            ],
            [
                'equipe' => $equipeMock1,
                'tdMis' => 4,
                'tdPris' => 1,
                'tdAverage' => 3,
                'pts' => 3
            ],
            [
                'equipe' => $equipeMock2,
                'tdMis' => 2,
                'tdPris' => 0,
                'tdAverage' => 2,
                'pts' => 2
            ],
        ];

        $classementService = new ClassementService($objectManager);

        $this->assertEquals($retour, $classementService->classementDetail(4));
    }
}