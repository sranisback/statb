<?php

namespace App\Tests\src\Service\EquipeService;

use App\Service\EquipeService;
use PHPUnit\Framework\TestCase;
use App\Entity\Teams;
use Doctrine\Common\Persistence\ObjectRepository;
use Doctrine\Common\Persistence\ManagerRegistry;

class toutesLesTeamsParAnne extends TestCase
{
    /**
     * @test
     */
    public function Est_Ce_Qu_on_Recoit_Une_Equipe()
    {
        $team = new Teams();
        $team->setYear(3);

        $teamRepo = $this->createMock(ObjectRepository::class);

        $teamRepo->expects($this->any())->method('findBy')->willReturn($team);

        $registryManager = $this->createMock(ManagerRegistry::class);

        $registryManager->expects($this->any())->method('getRepository')->willReturn($teamRepo);

        $testing = new EquipeService($registryManager);

        $this->assertEquals(1, count($testing->toutesLesTeamsParAnnee(3)));
    }

    /**
     * @test
     */
    public function Il_n_y_a_pas_d_equipes()
    {
        $teamRepo = $this->createMock(ObjectRepository::class);

        $teamRepo->expects($this->any())->method('findBy')->willReturn(NULL);

        $registryManager = $this->createMock(ManagerRegistry::class);

        $registryManager->expects($this->any())->method('getRepository')->willReturn($teamRepo);

        $testing = new EquipeService($registryManager);

        $this->assertEquals(0, count($testing->toutesLesTeamsParAnnee(3)));
    }

    /**
     * @test
     */
    public function il_y_a_plein_d_equipes()
    {
        for($x=0;$x<10;$x++){
            $team[$x] = new Teams();
            $team[$x]->setYear(3);
        }

        $teamRepo = $this->createMock(ObjectRepository::class);

        $teamRepo->expects($this->any())->method('findBy')->willReturn($team);

        $registryManager = $this->createMock(ManagerRegistry::class);

        $registryManager->expects($this->any())->method('getRepository')->willReturn($teamRepo);

        $testing = new EquipeService($registryManager);

        $this->assertEquals(10, count($testing->toutesLesTeamsParAnnee(3)));
    }
}
