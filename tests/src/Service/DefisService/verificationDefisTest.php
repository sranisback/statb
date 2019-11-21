<?php

namespace App\Tests\src\Service\DefisService;


use App\Entity\Defis;
use App\Entity\Matches;
use App\Entity\Teams;
use App\Service\DefisService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class verificationDefisTest extends KernelTestCase
{
    /**
     * @test
     */
    public function le_match_valide_un_defis()
    {
        $equipeMock0 = $this->createMock(Teams::class);
        $equipeMock0->method('getTeamId')->willReturn(0);
        $equipeMock1 = $this->createMock(Teams::class);
        $equipeMock1->method('getTeamId')->willReturn(0);

        $matchMock = $this->createMock(Matches::class);
        $matchMock->method('getTeam1')->willReturn($equipeMock0);
        $matchMock->method('getTeam2')->willReturn($equipeMock1);

        $defi = new Defis();

        $defi->setEquipeOrigine($equipeMock0);
        $defi->setEquipeDefiee($equipeMock1);
        $defi->setDefieRealise(0);

        $defiRepoMock = $this->getMockBuilder(Defis::class)
            ->setMethods(['listeDeDefisActifPourLeMatch'])
            ->getMock();
        $defiRepoMock->method('listeDeDefisActifPourLeMatch')->willReturn($defi);

        $objectManager = $this->createMock(EntityManagerInterface::class);
        $objectManager->expects($this->any())->method('getRepository')->will($this->returnCallback(
            function ($entityName) use ($defiRepoMock) {
                if ($entityName === 'App\Entity\Defis') {
                    return $defiRepoMock;
                }

                return true;
            }
        ));

        $defisService = new DefisService($objectManager);

        $testDefis = $defisService->verificationDefis($matchMock);

        $this->assertTrue($testDefis->getDefieRealise());
        $this->assertNotEmpty($testDefis->getMatchDefi());
    }

    /**
     * @test
     */
    public function il_n_y_a_pas_de_defis()
    {
        $equipeMock0 = $this->createMock(Teams::class);
        $equipeMock0->method('getTeamId')->willReturn(0);
        $equipeMock1 = $this->createMock(Teams::class);
        $equipeMock1->method('getTeamId')->willReturn(0);

        $matchMock = $this->createMock(Matches::class);
        $matchMock->method('getTeam1')->willReturn($equipeMock0);
        $matchMock->method('getTeam2')->willReturn($equipeMock1);

        $defiRepoMock = $this->getMockBuilder(Defis::class)
            ->setMethods(['listeDeDefisActifPourLeMatch'])
            ->getMock();
        $defiRepoMock->method('listeDeDefisActifPourLeMatch')->willReturn(null);

        $objectManager = $this->createMock(EntityManagerInterface::class);
        $objectManager->expects($this->any())->method('getRepository')->will($this->returnCallback(
            function ($entityName) use ($defiRepoMock) {
                if ($entityName === 'App\Entity\Defis') {
                    return $defiRepoMock;
                }

                return true;
            }
        ));

        $defisService = new DefisService($objectManager);

        $this->assertNull($defisService->verificationDefis($matchMock));
    }
}