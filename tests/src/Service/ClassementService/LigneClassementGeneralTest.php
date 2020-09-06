<?php


namespace App\Tests\src\Service\ClassementService;

use App\Entity\Matches;
use App\Entity\Teams;
use App\Service\ClassementService;
use App\Service\EquipeService;
use App\Service\MatchDataService;
use Doctrine\ORM\EntityManagerInterface;
use PHPUnit\Framework\TestCase;

class LigneClassementGeneralTest extends TestCase
{
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
            ->setMethods(['listeDesMatchs'])
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

        $objectManager = $this->createMock(EntityManagerInterface::class);
        $objectManager->method('getRepository')->willReturn($matchRepoMock);

        $equipeServiceMock = $this->createMock(EquipeService::class);
        $equipeServiceMock->method('resultatsDelEquipe')->willReturn(['win' => 1, 'draw' => 1, 'loss' => 1]);
        $equipeServiceMock->method('resultatDuMatch')->willReturnOnConsecutiveCalls(
            ['win' => 1, 'loss' => 0, 'draw' => 0],
            ['win' => 0, 'loss' => 1, 'draw' => 0],
            ['win' => 0, 'loss' => 0, 'draw' => 1],
        );

        $matchDateServiceMock = $this->createMock(MatchDataService::class);
        $matchDateServiceMock->method('nombreDeSortiesDunMatch')->willReturnOnConsecutiveCalls(2,5,1,2,0,5,0,1,0);

        $classementService = new ClassementService(
            $objectManager,
            $equipeServiceMock,
            $matchDateServiceMock
        );

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
                'sortiesContre' => 0
            ],
            $classementService->ligneClassementGeneral($equipeMock0,[8,3,-3])
        );
    }
}
