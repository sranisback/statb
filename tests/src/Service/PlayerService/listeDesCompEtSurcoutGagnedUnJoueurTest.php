<?php

namespace App\Tests\src\Service\PlayerService;

use App\Entity\GameDataSkills;
use App\Entity\Players;
use App\Entity\PlayersSkills;
use App\Service\EquipeService;
use App\Service\MatchDataService;
use App\Service\PlayerService;
use Doctrine\Common\Persistence\ObjectRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class listeDesCompEtSurcoutGagnedUnJoueurTest extends KernelTestCase
{
    /**
     * @test
     */
    public function toutes_les_comps_gagnees_sont_retournees()
    {
        $joueurMock = $this->createMock(Players::class);

        $gameDataSkillMock = $this->createMock(GameDataSkills::class);
        $gameDataSkillMock->method('getName')->willReturn('Block');

        $playersSkillsMock = $this->createMock(PlayersSkills::class);
        $playersSkillsMock->method('getType')->willReturn('N');
        $playersSkillsMock->method('getFSkill')->willReturn($gameDataSkillMock);

        $playersSkillsRepoMock = $this->createMock(ObjectRepository::class);
        $playersSkillsRepoMock->method('findBy')->willReturn([$playersSkillsMock]);

        $objectManager = $this->createMock(EntityManagerInterface::class);
        $objectManager->method('getRepository')->willReturn($playersSkillsRepoMock);

        $matchDataService = new MatchDataService($objectManager);

        $playerService = new PlayerService(
            $objectManager,
            $this->createMock(EquipeService::class),
            $matchDataService
        );

        $retour = ['compgagnee' => '<text class="text-success">Block</text>, ', 'cout' => 20000];

        $this->assertEquals($retour, $playerService->listeDesCompEtSurcoutGagnedUnJoueur($joueurMock));
    }
}