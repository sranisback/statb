<?php


namespace App\Tests\src\Service\ClassementService;


use App\Entity\Matches;
use App\Entity\Teams;
use App\Service\ClassementService;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Persistence\ObjectManager;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class classementDetailScoreGenTest extends KernelTestCase
{
    /**
     * @test
     */
    public function classement_des_equipes()
    {
        $equipeMock0 = $this->createMock(Teams::class);
        $equipeMock1 = $this->createMock(Teams::class);
        $equipeMock2 = $this->createMock(Teams::class);

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
            ->setMethods(['findBy'])
            ->getMock();

        $teamRepoMock->method('findBy')->willReturn(
            [$equipeMock0, $equipeMock1, $equipeMock2]
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

        $classementService = new ClassementService($objectManager);

        $retour = [
            [
                $equipeMock0,
                4,
                4,
                0
            ],
            [
                $equipeMock1,
                4,
                1,
                3
            ],
            [
                $equipeMock2,
                2,
                0,
                2
            ]
        ];

        $this->assertEquals($retour, $classementService->classementDetailScoreGen(4));
    }
}