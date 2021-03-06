<?php


namespace App\Tests\src\Service\ClassementService;

use App\Entity\Matches;
use App\Entity\Penalite;
use App\Entity\Teams;
use App\Service\ClassementService;
use App\Service\EquipeService;
use App\Service\MatchDataService;
use Doctrine\ORM\EntityManagerInterface;
use PHPUnit\Framework\TestCase;

class ToutesLesEquipesPourLeClassementGeneralTest extends TestCase
{
    /**
     * @test
     */
    public function une_liste_d_equipes_est_retournee()
    {
        $equipeMock0 = $this->createMock(Teams::class);
        $equipeMock0->method('getTeamId')->willReturn(0);

        $equipeMock1 = $this->createMock(Teams::class);
        $equipeMock1->method('getTeamId')->willReturn(1);

        $equipeMock2 = $this->createMock(Teams::class);
        $equipeMock2->method('getTeamId')->willReturn(2);

        $equipeMock3 = $this->createMock(Teams::class);
        $equipeMock3->method('getTeamId')->willReturn(3);

        $matchRepoMock = $this->getMockBuilder(Matches::class)
            ->addMethods(['listeDesMatchs'])
            ->getMock();

        $matchRepoMock->method('listeDesMatchs')->willReturn(
            []
        );

        $teamRepoMock = $this->getMockBuilder(Matches::class)
            ->setMethods(['findBy'])
            ->getMock();

        $listeEquipe = [$equipeMock0, $equipeMock1, $equipeMock2, $equipeMock3];

        $teamRepoMock->method('findBy')->willReturn(
            $listeEquipe
        );

        $penaliteRepoMock = $this->getMockBuilder(Penalite::class)
            ->setMethods(['penaliteDuneEquipe'])
            ->getMock();
        $penaliteRepoMock->method('penaliteDuneEquipe')->willReturnOnConsecutiveCalls(4,3,2,1);

        $objectManager = $this->createMock(EntityManagerInterface::class);
        $objectManager->method('getRepository')->will(
            $this->returnCallback(
                function ($entityName) use ($teamRepoMock, $matchRepoMock,$penaliteRepoMock) {
                    if ($entityName === 'App\Entity\Teams') {
                        return $teamRepoMock;
                    }

                    if ($entityName === 'App\Entity\Matches') {
                        return $matchRepoMock;
                    }

                    if ($entityName === 'App\Entity\Penalite') {
                        return $penaliteRepoMock;
                    }

                    return true;
                }
            )
        );

        $equipeServiceMock = $this->createMock(EquipeService::class);
        $equipeServiceMock->method('resultatsDelEquipe')->willReturnOnConsecutiveCalls(
            ['win' => 1, 'draw' => 1, 'loss' => 1],
            ['win' => 2, 'draw' => 0, 'loss' => 0],
            ['win' => 0, 'draw' => 2, 'loss' => 0],
            ['win' => 0, 'draw' => 0, 'loss' => 2]
        );

        $classementService = new ClassementService(
            $objectManager,
            $equipeServiceMock,
            $this->createMock(MatchDataService::class)
        );

        $resultat = $classementService->toutesLesEquipesPourLeClassementGeneral(0, [8,3,-3]);

        $this->assertEquals(
            [
                [
                    'gagne' => 1,
                    'nul' => 1,
                    'perdu' => 1,
                    'pts' => 8,
                    'bonus' => 0,
                    'equipe' => $equipeMock0,
                    'tdMis' => 0,
                    'tdPris' => 0,
                    'sortiesPour' => 0,
                    'sortiesContre' => 0,
                    'penalite' => 4
                ],
                [
                    'gagne' => 2,
                    'nul' => 0,
                    'perdu' => 0,
                    'pts' => 16,
                    'bonus' => 0,
                    'equipe' => $equipeMock1,
                    'tdMis' => 0,
                    'tdPris' => 0,
                    'sortiesPour' => 0,
                    'sortiesContre' => 0,
                    'penalite' => 3
                ],
                [
                    'gagne' => 0,
                    'nul' => 2,
                    'perdu' => 0,
                    'pts' => 6,
                    'bonus' => 0,
                    'equipe' => $equipeMock2,
                    'tdMis' => 0,
                    'tdPris' => 0,
                    'sortiesPour' => 0,
                    'sortiesContre' => 0,
                    'penalite' => 2
                ],
                [
                    'gagne' => 0,
                    'nul' => 0,
                    'perdu' => 2,
                    'pts' => -6,
                    'bonus' => 0,
                    'equipe' => $equipeMock3,
                    'tdMis' => 0,
                    'tdPris' => 0,
                    'sortiesPour' => 0,
                    'sortiesContre' => 0,
                    'penalite' => 1
                ]
            ],
            $resultat
        );

        $this->assertEquals(0, $resultat[0]['equipe']->getTeamId());
        $this->assertEquals(1, $resultat[1]['equipe']->getTeamId());
        $this->assertEquals(2, $resultat[2]['equipe']->getTeamId());
        $this->assertEquals(3, $resultat[3]['equipe']->getTeamId());
    }
}
