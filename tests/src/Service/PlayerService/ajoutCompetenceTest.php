<?php

namespace App\Tests\src\Service\PlayerService;


use App\Entity\GameDataSkills;
use App\Entity\Players;
use App\Tests\src\TestServiceFactory\PlayerServiceTestFactory;
use Doctrine\ORM\EntityManagerInterface;
use PHPUnit\Framework\TestCase;

class ajoutCompetenceTest extends TestCase
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

        $playerService = (new PlayerServiceTestFactory)->getInstance($objectManager);

        $playerService->ajoutCompetence($joueurTest, $gameDataSkillTest);

        $this->assertEquals(1,$joueurTest->getStatus());
    }
}