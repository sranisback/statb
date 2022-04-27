<?php

namespace App\Tests\src\Service\PlayerService;


use App\Entity\GameDataSkills;
use App\Entity\Players;
use App\Service\EquipeGestionService;
use App\Service\EquipeService;
use App\Service\InfosService;
use App\Service\MatchDataService;
use App\Service\PlayerService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class ajoutCompetenceTest extends KernelTestCase
{

    /**
     * @test
     */
    public function la_competence_s_ajoute_correctement(): void // pas tip top
    {
        $joueurTest = new Players();
        $joueurTest->setStatus(9);

        $gameDataSkillTest = new GameDataSkills();

        $objectManager = $this->createMock(EntityManagerInterface::class);

        $playerService = new PlayerService(
            $objectManager,
            $this->createMock(EquipeGestionService::class),
            $this->createMock(MatchDataService::class),
            $this->createMock(InfosService::class)
        );

        $playerService->ajoutCompetence($joueurTest, $gameDataSkillTest);

        $this->assertEquals(1,$joueurTest->getStatus());
    }
}