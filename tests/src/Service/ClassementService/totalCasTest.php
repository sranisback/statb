<?php

namespace App\Tests\src\Service\ClassementService;

use App\Entity\Matches;
use App\Service\ClassementService;
use App\Service\EquipeService;
use App\Service\MatchDataService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class totalCasTest extends KernelTestCase
{

    /**
     * @test
     */
    public function compte_les_cas_total_par_match(): void
    {
        $matchMock0 = $this->createMock(Matches::class);
        $matchMock1 = $this->createMock(Matches::class);
        $matchMock2 = $this->createMock(Matches::class);
        $matchMock3 = $this->createMock(Matches::class);
        $matchMock4 = $this->createMock(Matches::class);

        $matchRepoMock = $this->getMockBuilder(Matches::class)
            ->setMethods(['totalcas','tousLesMatchDuneAnne'])
            ->getMock();

        $matchRepoMock->method('totalcas')->willReturn(
            25
        );

        $matchRepoMock->method('tousLesMatchDuneAnne')->willReturn(
            [$matchMock0, $matchMock1, $matchMock2, $matchMock3, $matchMock4]
        );

        $objectManager = $this->createMock(EntityManagerInterface::class);
        $objectManager->method('getRepository')->willReturn($matchRepoMock);

        $classementService = new ClassementService(
            $objectManager,
            $this->createMock(EquipeService::class),
            $this->createMock(MatchDataService::class)
        );

        $tableauAttendu = ['score' => 25, 'nbrMatches' => 5, 'moyenne' => 5];

        $this->assertEquals($tableauAttendu, $classementService->totalCas(3));
    }

    /**
     * @test
     */
    public function il_n_y_a_pas_de_donnees(): void
    {
        $matchRepoMock = $this->getMockBuilder(Matches::class)
            ->setMethods(['totalcas','tousLesMatchDuneAnne'])
            ->getMock();

        $matchRepoMock->method('totalcas')->willReturn(
            0
        );

        $matchRepoMock->method('tousLesMatchDuneAnne')->willReturn(
            []
        );

        $objectManager = $this->createMock(EntityManagerInterface::class);
        $objectManager->method('getRepository')->willReturn($matchRepoMock);

        $classementService = new ClassementService(
            $objectManager,
            $this->createMock(EquipeService::class),
            $this->createMock(MatchDataService::class)
        );

        $tableauAttendu = ['score' => 0, 'nbrMatches' => 0, 'moyenne' => 0];

        $this->assertEquals($tableauAttendu, $classementService->totalCas(3));
    }
}
