<?php


namespace App\Tests\src\Service\ClassementService;


use App\Entity\Matches;
use App\Entity\Teams;
use App\Service\ClassementService;
use App\Service\EquipeService;
use App\Service\MatchDataService;
use Doctrine\ORM\EntityManagerInterface;
use PHPUnit\Framework\TestCase;

class CalculPointBonusTest extends TestCase
{
    /**
     * @test
     */
    public function le_bonus_sortie_est_bien_calcule()
    {
        $equipeMock = $this->createMock(Teams::class);

        $matchRepoMock = $this->getMockBuilder(Matches::class)
            ->setMethods(['listeDesMatchs'])
            ->getMock();

        $matchRepoMock->method('listeDesMatchs')->willReturn(
            [
                $this->createMock(Matches::class),
                $this->createMock(Matches::class),
                $this->createMock(Matches::class)
            ]
        );

        $objectManager = $this->createMock(EntityManagerInterface::class);
        $objectManager->method('getRepository')->willReturn($matchRepoMock);

        $matchDateServiceMock = $this->createMock(MatchDataService::class);
        $matchDateServiceMock->method('nombreDeSortiesDunMatch')->willReturnOnConsecutiveCalls(2,5,1);

        $equipeServiceMock = $this->createMock(EquipeService::class);
        $equipeServiceMock->method('resultatDuMatch')->willReturn(
            ['win' => 0, 'loss' => 0, 'draw' => 0],
        );

        $classementService = new ClassementService(
            $objectManager,
            $equipeServiceMock,
            $matchDateServiceMock
        );

        $this->assertEquals(1,$classementService->calculPointsBonus($equipeMock));
    }

    /**
     * @test
     */
    public function le_bonus_grande_victoire_est_bien_calcule()
    {
        $equipeMock = $this->createMock(Teams::class);

        $matchRepoMock = $this->getMockBuilder(Matches::class)
            ->setMethods(['listeDesMatchs'])
            ->getMock();

        $match0 = new Matches();
        $match0->setTeam1($equipeMock);
        $match0->setTeam1Score(2);

        $match1 = new Matches();
        $match1->setTeam2($equipeMock);
        $match1->setTeam2Score(3);

        $matchRepoMock->method('listeDesMatchs')->willReturn(
            [
                $match0,
                $this->createMock(Matches::class),
                $match1
            ]
        );

        $objectManager = $this->createMock(EntityManagerInterface::class);
        $objectManager->method('getRepository')->willReturn($matchRepoMock);

        $equipeServiceMock = $this->createMock(EquipeService::class);
        $equipeServiceMock->method('resultatDuMatch')->willReturnOnConsecutiveCalls(
            ['win' => 1, 'loss' => 0, 'draw' => 0],
            ['win' => 0, 'loss' => 1, 'draw' => 0],
            ['win' => 1, 'loss' => 0, 'draw' => 0]
        );

        $classementService = new ClassementService(
            $objectManager,
            $equipeServiceMock,
            $this->createMock(MatchDataService::class)
        );

        $this->assertEquals(1,$classementService->calculPointsBonus($equipeMock));
    }

    /**
     * @test
     */
    public function le_bonus_defense_et_petite_defaite_est_bien_calcule()
    {
        $equipeMock = $this->createMock(Teams::class);

        $matchRepoMock = $this->getMockBuilder(Matches::class)
            ->setMethods(['listeDesMatchs'])
            ->getMock();

        $match0 = new Matches();
        $match0->setTeam1($equipeMock);
        $match0->setTeam2Score(0);

        $match1 = new Matches();
        $match1->setTeam2($equipeMock);
        $match1->setTeam1Score(1);

        $matchRepoMock->method('listeDesMatchs')->willReturn(
            [
                $match0,
                $this->createMock(Matches::class),
                $match1
            ]
        );

        $objectManager = $this->createMock(EntityManagerInterface::class);
        $objectManager->method('getRepository')->willReturn($matchRepoMock);

        $equipeServiceMock = $this->createMock(EquipeService::class);
        $equipeServiceMock->method('resultatDuMatch')->willReturnOnConsecutiveCalls(
            ['win' => 0, 'loss' => 1, 'draw' => 0],
            ['win' => 0, 'loss' => 0, 'draw' => 1],
            ['win' => 0, 'loss' => 1, 'draw' => 0]
        );

        $classementService = new ClassementService(
            $objectManager,
            $equipeServiceMock,
            $this->createMock(MatchDataService::class)
        );

        $this->assertEquals(2,$classementService->calculPointsBonus($equipeMock));
    }

    /**
     * @test
     */
    public function le_bonus_intrepide_est_bien_calcule()
    {
        $equipeMock = $this->createMock(Teams::class);

        $matchRepoMock = $this->getMockBuilder(Matches::class)
            ->setMethods(['listeDesMatchs'])
            ->getMock();

        $match0 = new Matches();
        $match0->setTeam1($equipeMock);
        $match0->setTv1(1000000);
        $match0->setTv2(1500000);

        $match1 = new Matches();
        $match1->setTeam2($equipeMock);
        $match1->setTv1(1000000);
        $match1->setTv2(1500000);

        $matchRepoMock->method('listeDesMatchs')->willReturn(
            [
                $match0,
                $this->createMock(Matches::class),
                $match1
            ]
        );

        $objectManager = $this->createMock(EntityManagerInterface::class);
        $objectManager->method('getRepository')->willReturn($matchRepoMock);

        $equipeServiceMock = $this->createMock(EquipeService::class);
        $equipeServiceMock->method('resultatDuMatch')->willReturnOnConsecutiveCalls(
            ['win' => 1, 'loss' => 0, 'draw' => 0],
            ['win' => 0, 'loss' => 0, 'draw' => 1],
            ['win' => 1, 'loss' => 0, 'draw' => 0]
        );

        $classementService = new ClassementService(
            $objectManager,
            $equipeServiceMock,
            $this->createMock(MatchDataService::class)
        );

        $this->assertEquals(1,$classementService->calculPointsBonus($equipeMock));
    }
}