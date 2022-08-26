<?php

namespace App\Tests\src\Service\ClassementService;


use App\Entity\Matches;
use App\Service\ClassementService;
use App\Service\EquipeService;
use App\Service\MatchDataService;
use App\Service\SettingsService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class cinqDernierMatchesTest extends KernelTestCase
{

    private ClassementService $classementService;

    private $objectManager;

    public function setUp(): void
    {
        parent::setUp();

        $this->objectManager = $this->createMock(EntityManagerInterface::class);

        $this->classementService = new ClassementService(
            $this->objectManager,
            $this->createMock(EquipeService::class),
            $this->createMock(MatchDataService::class),
            $this->createMock(SettingsService::class)
        );
    }

    /**
     * @test
     */
    public function affiche_cinq_dernier_matchs(): void
    {
        $matchMock0 = $this->createMock(Matches::class);
        $matchMock1 = $this->createMock(Matches::class);
        $matchMock2 = $this->createMock(Matches::class);
        $matchMock3 = $this->createMock(Matches::class);
        $matchMock4 = $this->createMock(Matches::class);
        $matchMock5 = $this->createMock(Matches::class);

        $matchRepoMock = $this->getMockBuilder(Matches::class)
            ->addMethods(['tousLesMatchDuneAnneClassementChrono'])
            ->getMock();

        $matchRepoMock->method('tousLesMatchDuneAnneClassementChrono')->willReturn(
            [$matchMock0, $matchMock1, $matchMock2, $matchMock3, $matchMock4, $matchMock5]
        );

        $this->objectManager->method('getRepository')->willReturn($matchRepoMock);

        $this->assertEquals(5, count($this->classementService->cinqDerniersMatchsParAnnee(3)));
    }

    /**
     * @test
     */
    public function il_n_y_a_pas_de_matches(): void
    {
        $matchRepoMock = $this->getMockBuilder(Matches::class)
            ->addMethods(['tousLesMatchDuneAnneClassementChrono'])
            ->getMock();

        $matchRepoMock->method('tousLesMatchDuneAnneClassementChrono')->willReturn([]);

        $this->objectManager->method('getRepository')->willReturn($matchRepoMock);


        $this->assertEquals(0, count($this->classementService->cinqDerniersMatchsParAnnee(3)));
    }
}