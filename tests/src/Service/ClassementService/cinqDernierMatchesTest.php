<?php

namespace App\Tests\src\Service\ClassementService;


use App\Entity\Matches;
use App\Service\ClassementService;
use App\Service\EquipeService;
use App\Service\MatchDataService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class cinqDernierMatchesTest extends KernelTestCase
{
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
            ->setMethods(['tousLesMatchDuneAnneClassementChrono'])
            ->getMock();

        $matchRepoMock->method('tousLesMatchDuneAnneClassementChrono')->willReturn(
            [$matchMock0, $matchMock1, $matchMock2, $matchMock3, $matchMock4, $matchMock5]
        );

        $objectManager = $this->createMock(EntityManagerInterface::class);
        $objectManager->method('getRepository')->willReturn($matchRepoMock);

        $classementService = new ClassementService(
            $objectManager,
            $this->createMock(EquipeService::class),
            $this->createMock(MatchDataService::class)
        );

        $this->assertEquals(5, count($classementService->cinqDerniersMatchsParAnnee(3)));
    }

    /**
     * @test
     */
    public function il_n_y_a_pas_de_matches(): void
    {
        $matchRepoMock = $this->getMockBuilder(Matches::class)
            ->setMethods(['tousLesMatchDuneAnneClassementChrono'])
            ->getMock();

        $matchRepoMock->method('tousLesMatchDuneAnneClassementChrono')->willReturn([]);

        $objectManager = $this->createMock(EntityManagerInterface::class);
        $objectManager->method('getRepository')->willReturn($matchRepoMock);

        $classementService = new ClassementService(
            $objectManager,
            $this->createMock(EquipeService::class),
            $this->createMock(MatchDataService::class)
        );

        $this->assertEquals(0, count($classementService->cinqDerniersMatchsParAnnee(3)));
    }
}