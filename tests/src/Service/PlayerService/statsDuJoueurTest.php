<?php

namespace App\Tests\src\Service\PlayerService;

use App\Entity\GameDataPlayers;
use App\Entity\GameDataSkills;
use App\Entity\MatchData;
use App\Entity\Players;
use App\Entity\PlayersSkills;
use App\Service\EquipeService;
use App\Service\MatchDataService;
use App\Service\PlayerService;
use Doctrine\Common\Persistence\ObjectRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class statsDuJoueurTest extends KernelTestCase
{
    /**
     * @test
     */
    public function les_stats_du_joueur_sont_bien_retournee()
    {
        $positionmock = $this->createMock(GameDataPlayers::class);
        $positionmock->method('getSkills')->willReturn('5,23,24');

        $joueurMock = $this->createMock(Players::class);
        $joueurMock->method('getFPos')->willReturn($positionmock);
        $joueurMock->method('getType')->willReturn(1);

        $matchDataMock0 = $this->createMock(MatchData::class);
        $matchDataMock0->method('getMvp')->willReturn(1);

        $matchDataRepoMock = $this->createMock(ObjectRepository::class);
        $matchDataRepoMock->method('findBy')->willReturn([$matchDataMock0]);

        $gameDataSkillMock = $this->createMock(GameDataSkills::class);
        $gameDataSkillMock->method('getName')->willReturn('Block');

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

        $playersSkillsMock = $this->createMock(PlayersSkills::class);
        $playersSkillsMock->method('getType')->willReturn('N');
        $playersSkillsMock->method('getFSkill')->willReturn($gameDataSkillMock);

        $playersSkillsRepoMock = $this->createMock(ObjectRepository::class);
        $playersSkillsRepoMock->method('findBy')->willReturn([$playersSkillsMock]);

        $objectManager = $this->createMock(EntityManagerInterface::class);
        $objectManager->method('getRepository')->will($this->returnCallback(
            function ($entityName) use ($matchDataRepoMock, $gameDataSkillRepoMock, $playersSkillsRepoMock) {
                if ($entityName === 'App\Entity\MatchData') {
                    return $matchDataRepoMock;
                }

                if ($entityName === 'App\Entity\GameDataSkills') {
                    return $gameDataSkillRepoMock;
                }

                if ($entityName === 'App\Entity\PlayersSkills') {
                    return $playersSkillsRepoMock;
                }
                return true;
            }
        ));

        $matchDataService = new MatchDataService($objectManager);

        $playerService = new PlayerService(
            $objectManager,
            $this->createMock(EquipeService::class),
            $matchDataService
        );

        $retour['comp'] = '<text class="test-primary">Frenzy</text>, <text class="test-primary">Dodge</text>, <text class="test-primary">Jump Up</text>, <text class="text-success">Block</text>';
        $retour['actions'] = [
            'NbrMatch' => 1,
            'cp' => 0,
            'td' => 0,
            'int' => 0,
            'cas' => 0,
            'mvp' => 1,
            'agg' => 0,
        ];

        $this->assertEquals($retour, $playerService->statsDuJoueur($joueurMock));
    }
}