<?php

namespace App\Tests\src\Service\ClassementService;

use App\Entity\Matches;
use App\Entity\Teams;
use App\Service\ClassementService;
use Doctrine\Persistence\ObjectManager;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class classementDetailScoreDuneEquipeTest extends KernelTestCase
{
    /**
     * @test
     */
    public function le_total_td_est_retourne()
    {
        $equipeMock = $this->createMock(Teams::class);

        $matchMock0 = $this->createMock(Matches::class);
        $matchMock0->method('getTeam1')->willReturn($equipeMock);
        $matchMock0->method('getTeam1Score')->willReturn(2);
        $matchMock0->method('getTeam2Score')->willReturn(2);
        $matchMock1 = $this->createMock(Matches::class);
        $matchMock1->method('getTeam1')->willReturn($equipeMock);
        $matchMock1->method('getTeam1Score')->willReturn(2);
        $matchMock1->method('getTeam2Score')->willReturn(2);
        $matchMock2 = $this->createMock(Matches::class);
        $matchMock2->method('getTeam1')->willReturn($equipeMock);
        $matchMock2->method('getTeam1Score')->willReturn(2);
        $matchMock2->method('getTeam2Score')->willReturn(1);
        $matchMock3 = $this->createMock(Matches::class);
        $matchMock3->method('getTeam1')->willReturn($equipeMock);
        $matchMock3->method('getTeam1Score')->willReturn(2);
        $matchMock4 = $this->createMock(Matches::class);
        $matchMock4->method('getTeam1')->willReturn($equipeMock);
        $matchMock4->method('getTeam1Score')->willReturn(2);
        $matchMock5 = $this->createMock(Matches::class);
        $matchMock5->method('getTeam1')->willReturn($equipeMock);
        $matchMock5->method('getTeam1Score')->willReturn(0);

        $matchRepoMock = $this->getMockBuilder(Matches::class)
            ->setMethods(['listeDesMatchs'])
            ->getMock();

        $matchRepoMock->method('listeDesMatchs')->willReturn(
            [$matchMock0, $matchMock1, $matchMock2, $matchMock3, $matchMock4, $matchMock5]
        );

        $teamRepoMock = $this->createMock(ObjectManager::class);

        $objectManager = $this->createMock(EntityManagerInterface::class);
        $classementService = new ClassementService($objectManager);
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
            'equipe' => $equipeMock,
            'tdMis' => 10,
            'tdPris' => 5,
            'tdAverage' => 5
        ];

        $this->assertEquals($retour, $classementService->classementDetailScoreDuneEquipe($equipeMock));
    }

    /**
     * @test
     */
    public function il_n_y_a_pas_de_donnees()
    {
        $equipeMock = $this->createMock(Teams::class);

        $matchRepoMock = $this->getMockBuilder(Matches::class)
            ->setMethods(['listeDesMatchs'])
            ->getMock();

        $matchRepoMock->method('listeDesMatchs')->willReturn(
            []
        );

        $teamRepoMock = $this->createMock(ObjectManager::class);

        $objectManager = $this->createMock(EntityManagerInterface::class);

        $classementService = new ClassementService($objectManager);
        $objectManager->expects($this->any())->method('getRepository')->will(
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
            'equipe' => $equipeMock,
            'tdMis' => 0,
            'tdPris' => 0,
            'tdAverage' => 0
        ];

        $this->assertEquals($retour, $classementService->classementDetailScoreDuneEquipe($equipeMock));
    }

}