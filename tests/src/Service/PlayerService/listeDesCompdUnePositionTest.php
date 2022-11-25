<?php

namespace App\Tests\src\Service\PlayerService;

use App\Entity\GameDataPlayers;
use App\Entity\GameDataSkills;
use App\Service\MatchDataService;
use App\Tests\src\TestServiceFactory\PlayerServiceTestFactory;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\EntityManagerInterface;
use PHPUnit\Framework\TestCase;

class listeDesCompdUnePositionTest extends TestCase
{
    /**
     * @test
     */
    public function toutes_les_comps_sont_retournees(): void
    {
        $gameDataSkillMock0 = $this->createMock(GameDataSkills::class);
        $gameDataSkillMock0->method('getName')->willReturnOnConsecutiveCalls('Frenzy');

        $gameDataSkillMock1 = $this->createMock(GameDataSkills::class);
        $gameDataSkillMock1->method('getName')->willReturnOnConsecutiveCalls('Dodge');

        $gameDataSkillMock2 = $this->createMock(GameDataSkills::class);
        $gameDataSkillMock2->method('getName')->willReturnOnConsecutiveCalls('Jump Up');

        $testbaseSkills = new ArrayCollection([$gameDataSkillMock0, $gameDataSkillMock1, $gameDataSkillMock2]);

        $positionmock = $this->createMock(GameDataPlayers::class);
        $positionmock->method('getBaseSkills')->willReturn(
            $testbaseSkills
        );

        $objectManager = $this->createMock(EntityManagerInterface::class);

        $matchDataService = new MatchDataService($objectManager);

        $playerService = (new PlayerServiceTestFactory())->getInstance(
            $objectManager,
            $matchDataService
        );

        $retour = '<text class="test-primary">Frenzy</text>, <text class="test-primary">Dodge</text>, <text class="test-primary">Jump Up</text>, ';

        $this->assertEquals($retour,$playerService->listeDesCompdUnePosition($positionmock));
    }

}