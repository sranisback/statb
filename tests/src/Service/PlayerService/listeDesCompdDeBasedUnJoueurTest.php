<?php

namespace App\Tests\src\Service\PlayerService;

use App\Entity\GameDataPlayers;
use App\Entity\GameDataSkills;
use App\Entity\Players;
use App\Service\EquipeService;
use App\Service\MatchDataService;
use App\Service\PlayerService;
use Doctrine\Common\Persistence\ObjectRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class listeDesCompdDeBasedUnJoueurTest extends KernelTestCase
{
    /**
     * @test
     */
    public function toutes_les_comps_de_base_sont_retournees()
    {
        $positionmock = $this->createMock(GameDataPlayers::class);
        $positionmock->method('getSkills')->willReturn('5,23,24');

        $joueurMock = $this->createMock(Players::class);
        $joueurMock->method('getFPos')->willReturn($positionmock);

        $gameDataSkillMock0 = $this->createMock(GameDataSkills::class);
        $gameDataSkillMock0->method('getName')->willReturnOnConsecutiveCalls('Frenzy');

        $gameDataSkillMock1 = $this->createMock(GameDataSkills::class);
        $gameDataSkillMock1->method('getName')->willReturnOnConsecutiveCalls('Dodge');

        $gameDataSkillMock2 = $this->createMock(GameDataSkills::class);
        $gameDataSkillMock2->method('getName')->willReturnOnConsecutiveCalls('Jump Up');

        $gameDataSkillRepoMock = $this->createMock(ObjectRepository::class);
        $gameDataSkillRepoMock->method('findOneBy')->willReturnOnConsecutiveCalls(
            $gameDataSkillMock0,
            $gameDataSkillMock1,
            $gameDataSkillMock2
        );

        $objectManager = $this->createMock(EntityManagerInterface::class);
        $objectManager->method('getRepository')->willReturn($gameDataSkillRepoMock);

        $matchDataService = new MatchDataService($objectManager);

        $playerService = new PlayerService(
            $objectManager,
            $this->createMock(EquipeService::class),
            $matchDataService
        );

        $retour = '<text class="test-primary">Frenzy</text>, <text class="test-primary">Dodge</text>, <text class="test-primary">Jump Up</text>, ';

        $this->assertEquals($playerService->listeDesCompdDeBasedUnJoueur($joueurMock), $retour);
    }
}