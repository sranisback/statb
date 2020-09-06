<?php

namespace App\Tests\src\Service\ClassementService;

use App\Entity\Matches;
use App\Entity\Teams;
use App\Service\ClassementService;
use App\Service\EquipeService;
use App\Service\MatchDataService;
use Doctrine\Persistence\ObjectManager;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class classementDetailScoreDuneEquipeTest extends KernelTestCase
{
    /**
     * @test
     */
    public function le_total_td_est_retourne(): void
    {
        $equipeMock = $this->createMock(Teams::class);

        $matchMock0 = $this->createMock(Matches::class);
        $matchMock0->method('getTeam1')->willReturn($equipeMock);
        $matchMock0->method('getTeam2')->willReturn($this->createMock(Teams::class));
        $matchMock0->method('getTeam1Score')->willReturn(2);
        $matchMock0->method('getTeam2Score')->willReturn(2);
        $matchMock1 = $this->createMock(Matches::class);
        $matchMock1->method('getTeam1')->willReturn($equipeMock);
        $matchMock1->method('getTeam2')->willReturn($this->createMock(Teams::class));
        $matchMock1->method('getTeam1Score')->willReturn(2);
        $matchMock1->method('getTeam2Score')->willReturn(2);

        $matchRepoMock = $this->getMockBuilder(Matches::class)
            ->setMethods(['listeDesMatchs'])
            ->getMock();

        $matchRepoMock->method('listeDesMatchs')->willReturn(
            [$matchMock0, $matchMock1]
        );

        $teamRepoMock = $this->createMock(ObjectManager::class);

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

        $matchDataServiceMock = $this->createMock(MatchDataService::class);
        $matchDataServiceMock->method('nombreDeSortiesDunMatch')->willReturnOnConsecutiveCalls(3,2,1,0);

        $classementService = new ClassementService(
            $objectManager,
            $this->createMock(EquipeService::class),
            $matchDataServiceMock
        );


        $this->assertEquals([
            'tdMis' => 4,
            'tdPris' => 4,
            'sortiesPour' => 4,
            'sortiesContre' => 2,
        ], $classementService->classementDetailScoreDuneEquipe($equipeMock));
    }

    /**
     * @test
     */
    public function il_n_y_a_pas_de_donnees(): void
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

        $classementService = new ClassementService(
            $objectManager,
            $this->createMock(EquipeService::class),
            $this->createMock(MatchDataService::class)
        );

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
            'tdMis' => 0,
            'tdPris' => 0,
            'sortiesPour' => 0,
            'sortiesContre' => 0,
        ];

        $this->assertEquals($retour, $classementService->classementDetailScoreDuneEquipe($equipeMock));
    }

}