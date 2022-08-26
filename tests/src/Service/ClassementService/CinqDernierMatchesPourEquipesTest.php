<?php

namespace App\Tests\src\Service\ClassementService;

use App\Entity\Matches;
use App\Entity\Teams;
use App\Service\ClassementService;
use App\Service\EquipeService;
use App\Service\MatchDataService;
use App\Service\SettingsService;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Persistence\ObjectRepository;
use PHPUnit\Framework\TestCase;

class CinqDernierMatchesPourEquipesTest extends TestCase
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
    public function les_cinqs_dernier_matchs_sont_retournes(): void
    {
        $matchMock0 = $this->createMock(Matches::class);
        $matchMock1 = $this->createMock(Matches::class);
        $matchMock2 = $this->createMock(Matches::class);
        $matchMock3 = $this->createMock(Matches::class);
        $matchMock4 = $this->createMock(Matches::class);
        $matchMock5 = $this->createMock(Matches::class);

        $equipeMock = $this->createMock(Teams::class);

        $matchRepoMock = $this->getMockBuilder(Matches::class)
            ->addMethods(['listeDesMatchs'])
            ->getMock();
        $matchRepoMock->method('listeDesMatchs')->willReturn(
            [$matchMock0, $matchMock1, $matchMock2, $matchMock3, $matchMock4, $matchMock5]
        );

        $teamRepoMock = $this->createMock(ObjectRepository::class);
        $teamRepoMock->method('findOneBy')->willReturn($equipeMock);

        $this->objectManager->expects($this->any())->method('getRepository')->will(
            $this->returnCallback(
                function ($entityName) use ($teamRepoMock, $matchRepoMock) {
                    if ($entityName === Teams::class) {
                        return $teamRepoMock;
                    }

                    if ($entityName === Matches::class) {
                        return $matchRepoMock;
                    }

                    return true;
                }
            )
        );

        $this->assertEquals(5, count($this->classementService->cinqDerniersMatchsParEquipe(0)));
    }

    /**
     * @test
     */
    public function pas_de_matchs_donc_pas_de_retours(): void
    {
        $equipeMock = $this->createMock(Teams::class);

        $matchRepoMock = $this->getMockBuilder(Matches::class)
            ->addMethods(['listeDesMatchs'])
            ->getMock();
        $matchRepoMock->method('listeDesMatchs')->willReturn([]);

        $teamRepoMock = $this->createMock(ObjectRepository::class);
        $teamRepoMock->method('findOneBy')->willReturn($equipeMock);

        $this->objectManager->expects($this->any())->method('getRepository')->will(
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

        $this->assertEquals(0, count($this->classementService->cinqDerniersMatchsParEquipe(0)));
    }
}
