<?php

namespace App\Tests\src\Service\PlayerService;

use App\Entity\GameDataSkills;
use App\Entity\Players;
use App\Entity\PlayersSkills;
use App\Service\EquipeGestionService;
use App\Service\EquipeService;
use App\Service\InfosService;
use App\Service\MatchDataService;
use App\Service\PlayerService;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\EntityManagerInterface;
use PHPUnit\Framework\TestCase;

class listeDesCompEtSurcoutGagnedUnJoueurTest extends TestCase
{
    /**
     * @test
     */
    public function une_comp_gagnee_est_retournee(): void
    {
        $gameDataSkillMock = $this->createMock(GameDataSkills::class);
        $gameDataSkillMock->method('getName')->willReturn('Block');

        $playersSkillsMock = $this->createMock(PlayersSkills::class);
        $playersSkillsMock->method('getType')->willReturn('N');
        $playersSkillsMock->method('getFSkill')->willReturn($gameDataSkillMock);

        $skillsMock = new ArrayCollection([$playersSkillsMock]);

        $joueurMock = $this->createMock(Players::class);
        $joueurMock->method('getSkills')->willReturn($skillsMock);

        $objectManager = $this->createMock(EntityManagerInterface::class);

        $matchDataService = new MatchDataService($objectManager);

        $playerService = new PlayerService(
            $objectManager,
            $this->createMock(EquipeGestionService::class),
            $matchDataService,
            $this->createMock(InfosService::class)
        );

        $retour = ['compgagnee' => '<text class="text-success">Block</text>, ', 'cout' => 20_000];

        $this->assertEquals($retour, $playerService->listeDesCompEtSurcoutGagnedUnJoueur($joueurMock));
    }

    /**
     * @test
     */
    public function toutes_les_comps_gagnees_sont_retournees(): void
    {
        $gameDataSkillMock0 = $this->createMock(GameDataSkills::class);
        $gameDataSkillMock0->method('getName')->willReturn('Block');

        $gameDataSkillMock1 = $this->createMock(GameDataSkills::class);
        $gameDataSkillMock1->method('getName')->willReturn('Esquive');

        $playersSkillsMock0 = $this->createMock(PlayersSkills::class);
        $playersSkillsMock0->method('getType')->willReturn('N');
        $playersSkillsMock0->method('getFSkill')->willReturn($gameDataSkillMock0);

        $playersSkillsMock1 = $this->createMock(PlayersSkills::class);
        $playersSkillsMock1->method('getType')->willReturn('D');
        $playersSkillsMock1->method('getFSkill')->willReturn($gameDataSkillMock1);

        $skillsMock = new ArrayCollection([$playersSkillsMock0, $playersSkillsMock1]);

        $joueurMock = $this->createMock(Players::class);
        $joueurMock->method('getSkills')->willReturn($skillsMock);

        $objectManager = $this->createMock(EntityManagerInterface::class);

        $matchDataService = new MatchDataService($objectManager);

        $playerService = new PlayerService(
            $objectManager,
            $this->createMock(EquipeGestionService::class),
            $matchDataService,
            $this->createMock(InfosService::class)
        );

        $retour = ['compgagnee' => '<text class="text-success">Block</text>, <text class="text-danger">Esquive</text>, ', 'cout' => 50_000];

        $this->assertEquals($retour, $playerService->listeDesCompEtSurcoutGagnedUnJoueur($joueurMock));
    }
}