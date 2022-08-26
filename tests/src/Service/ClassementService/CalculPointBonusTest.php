<?php


namespace App\Tests\src\Service\ClassementService;


use App\Entity\Matches;
use App\Entity\Teams;
use App\Service\ClassementService;
use App\Service\EquipeService;
use App\Service\MatchDataService;
use App\Service\SettingsService;
use Doctrine\ORM\EntityManagerInterface;
use PHPUnit\Framework\TestCase;

class CalculPointBonusTest extends TestCase
{
    private $equipeServiceMock;

    private $objectManager;

    private $matchDateServiceMock;

    private $classementService;

    public function setUp(): void
    {
        parent::setUp();

        $this->equipeServiceMock = $this->createMock(EquipeService::class);

        $this->objectManager = $this->createMock(EntityManagerInterface::class);

        $this->matchDateServiceMock = $this->createMock(MatchDataService::class);

        $this->classementService = new ClassementService(
            $this->objectManager,
            $this->equipeServiceMock,
            $this->matchDateServiceMock,
            $this->createMock(SettingsService::class)
        );

    }

    /**
     * @test
     */
    public function le_bonus_sortie_est_bien_calcule()
    {
        $equipeMock = $this->createMock(Teams::class);

        $matchRepoMock = $this->getMockBuilder(Matches::class)
            ->addMethods(['listeDesMatchs'])
            ->getMock();

        $matchRepoMock->method('listeDesMatchs')->willReturn(
            [
                $this->createMock(Matches::class),
                $this->createMock(Matches::class),
                $this->createMock(Matches::class)
            ]
        );

        $this->objectManager->method('getRepository')->willReturn($matchRepoMock);

        $this->matchDateServiceMock->method('nombreDeSortiesDunMatch')->willReturnOnConsecutiveCalls(2,5,1);

        $this->equipeServiceMock->method('resultatDuMatch')->willReturn(
            ['win' => 0, 'loss' => 0, 'draw' => 0],
        );

        $this->assertEquals(1,$this->classementService->calculPointsBonus($equipeMock));
    }

    /**
     * @test
     */
    public function le_bonus_grande_victoire_est_bien_calcule()
    {
        $equipeMock = $this->createMock(Teams::class);

        $match0 = new Matches();
        $match0->setTeam1($equipeMock);
        $match0->setTeam1Score(3);

        $matchRepoMock = $this->getMockBuilder(Matches::class)
            ->addMethods(['listeDesMatchs'])
            ->getMock();

        $matchRepoMock->method('listeDesMatchs')->willReturn(
            [
                $match0
            ]
        );

        $this->objectManager->method('getRepository')->willReturn($matchRepoMock);

        $this->equipeServiceMock->method('resultatDuMatch')->willReturnOnConsecutiveCalls(
            ['win' => 1, 'loss' => 0, 'draw' => 0],
            ['win' => 1, 'loss' => 0, 'draw' => 0],
            ['win' => 1, 'loss' => 0, 'draw' => 0],
            ['win' => 1, 'loss' => 0, 'draw' => 0],
            ['win' => 1, 'loss' => 0, 'draw' => 0]
        );

        $this->assertEquals(1, $this->classementService->calculPointsBonus($equipeMock));
    }

    /**
     * @test
     */
    public function le_bonus_defense_et_petite_defaite_est_bien_calcule()
    {
        $equipeMock = $this->createMock(Teams::class);


        $match0 = new Matches();
        $match0->setTeam2($equipeMock);
        $match0->setTeam1Score(1);

        $match1 = new Matches();

        $matchRepoMock = $this->getMockBuilder(Matches::class)
            ->addMethods(['listeDesMatchs'])
            ->getMock();
        $matchRepoMock->method('listeDesMatchs')->willReturn(
            [
                $match0,
                $match1
            ]
        );

        $this->objectManager->method('getRepository')->willReturn($matchRepoMock);

        $this->equipeServiceMock->method('resultatDuMatch')->willReturnOnConsecutiveCalls(
            ['win' => 0, 'loss' => 1, 'draw' => 0],
            ['win' => 0, 'loss' => 1, 'draw' => 0],
            ['win' => 0, 'loss' => 1, 'draw' => 0],
            ['win' => 0, 'loss' => 1, 'draw' => 0],
            ['win' => 0, 'loss' => 1, 'draw' => 0],
            ['win' => 0, 'loss' => 0, 'draw' => 1],
            ['win' => 0, 'loss' => 0, 'draw' => 1],
            ['win' => 0, 'loss' => 0, 'draw' => 1],
            ['win' => 0, 'loss' => 0, 'draw' => 1],
            ['win' => 0, 'loss' => 0, 'draw' => 1]
        );

        $this->assertEquals(2, $this->classementService->calculPointsBonus($equipeMock));
    }

    /**
     * @test
     */
    public function le_bonus_intrepide_est_bien_calcule()
    {
        $equipeMock = $this->createMock(Teams::class);

        $match0 = new Matches();
        $match0->setTeam1($equipeMock);
        $match0->setTv1(1000000);
        $match0->setTv2(1500000);

        $match1 = new Matches();
        $match1->setTeam2($equipeMock);
        $match1->setTv1(1000000);
        $match1->setTv2(1500000);

        $matchRepoMock = $this->getMockBuilder(Matches::class)
            ->addMethods(['listeDesMatchs'])
            ->getMock();
        $matchRepoMock->method('listeDesMatchs')->willReturn(
            [
                $match0,
                $this->createMock(Matches::class),
                $match1
            ]
        );

        $this->objectManager->method('getRepository')->willReturn($matchRepoMock);

        $this->equipeServiceMock->method('resultatDuMatch')->willReturnOnConsecutiveCalls(
            ['win' => 1, 'loss' => 0, 'draw' => 0],
            ['win' => 1, 'loss' => 0, 'draw' => 0],
            ['win' => 1, 'loss' => 0, 'draw' => 0],
            ['win' => 1, 'loss' => 0, 'draw' => 0],
            ['win' => 1, 'loss' => 0, 'draw' => 0],
            ['win' => 0, 'loss' => 0, 'draw' => 1],
            ['win' => 0, 'loss' => 0, 'draw' => 1],
            ['win' => 0, 'loss' => 0, 'draw' => 1],
            ['win' => 0, 'loss' => 0, 'draw' => 1],
            ['win' => 0, 'loss' => 0, 'draw' => 1],
            ['win' => 1, 'loss' => 0, 'draw' => 0],
            ['win' => 1, 'loss' => 0, 'draw' => 0],
            ['win' => 1, 'loss' => 0, 'draw' => 0],
            ['win' => 1, 'loss' => 0, 'draw' => 0],
            ['win' => 1, 'loss' => 0, 'draw' => 0]
        );

        $this->assertEquals(1, $this->classementService->calculPointsBonus($equipeMock));
    }
}