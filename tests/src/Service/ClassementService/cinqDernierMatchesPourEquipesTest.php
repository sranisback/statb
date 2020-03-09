<?php

namespace App\Tests\src\Service\ClassementService;


use App\Entity\Matches;
use App\Entity\Teams;
use App\Service\ClassementService;
use Doctrine\Common\Persistence\ObjectRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class cinqDernierMatchesPourEquipesTest extends KernelTestCase
{

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
            ->setMethods(['listeDesMatchs'])
            ->getMock();

        $matchRepoMock->method('listeDesMatchs')->willReturn(
            [$matchMock0, $matchMock1, $matchMock2, $matchMock3, $matchMock4, $matchMock5]
        );

        $teamRepoMock = $this->createMock(ObjectRepository::class);
        $teamRepoMock->method('findOneBy')->willReturn($equipeMock);

        $objectManager = $this->createMock(EntityManagerInterface::class);
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
        $classementService = new ClassementService($objectManager);

        $this->assertEquals(5, count($classementService->cinqDerniersMatchsParEquipe(0)));
    }

    /**
     * @test
     */
    public function pas_de_matchs_donc_pas_de_retours(): void
    {
        $equipeMock = $this->createMock(Teams::class);

        $matchRepoMock = $this->getMockBuilder(Matches::class)
            ->setMethods(['listeDesMatchs'])
            ->getMock();

        $matchRepoMock->method('listeDesMatchs')->willReturn([]);

        $teamRepoMock = $this->createMock(ObjectRepository::class);
        $teamRepoMock->method('findOneBy')->willReturn($equipeMock);

        $objectManager = $this->createMock(EntityManagerInterface::class);
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

        $classementService = new ClassementService($objectManager);

        $this->assertEquals(0, count($classementService->cinqDerniersMatchsParEquipe(0)));
    }
}