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

        $match = new Matches();
        $match->setTeam1($equipeMock0);
        $match->setTeam1Score(1);

        $matchRepoMock = $this->getMockBuilder(Matches::class)
            ->setMethods(['listeDesMatchs'])
            ->getMock();

        $matchRepoMock->method('listeDesMatchs')->willReturnOnConsecutiveCalls(
            [],[
                $this->createMock(Matches::class),
                $match,
                $this->createMock(Matches::class)
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
        $matchDateServiceMock->method('nombreDeSortiesDunMatch')->willReturnOnConsecutiveCalls(2,5,1);

        $classementService = new ClassementService(
            $objectManager,
            $equipeServiceMock,
            $matchDateServiceMock
        );

        $this->assertEquals(['G' => 1, 'N' => 1, 'P' => 1, 'pts' => 10, 'bonus' => 2, 'equipe' => $equipeMock0, 'nbrg' => 3],
            $classementService->ligneClassementGeneral($equipeMock0,[8,3,-3])
        );
    }
}
