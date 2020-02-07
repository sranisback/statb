<?php


namespace App\Tests\src\Service\PlayerService;

use App\Entity\GameDataPlayers;
use App\Entity\GameDataSkills;
use App\Entity\Players;
use App\Entity\PlayersSkills;
use App\Entity\Teams;
use App\Service\EquipeService;
use App\Service\MatchDataService;
use App\Service\PlayerService;
use Doctrine\Common\Persistence\ObjectRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class coutTotalJoueursTest extends KernelTestCase
{
    /**
     * @test
     */
    public function le_cout_total_des_joueurs_est_bien_calcule(): void
    {
        $equipeMock = $this->createMock(Teams::class);

        $positionMock = $this->createMock(GameDataPlayers::class);
        $positionMock->method('getCost')->willReturn(110_000);

        $joueurMock = $this->createMock(Players::class);
        $joueurMock->method('getFPos')->willReturn($positionMock);

        $gameDataSkillMock = $this->createMock(GameDataSkills::class);
        $gameDataSkillMock->method('getName')->willReturn('block');

        $playersSkillMock = $this->createMock(PlayersSkills::class);
        $playersSkillMock->method('getType')->willReturn('N');
        $playersSkillMock->method('getFSkill')->willReturn($gameDataSkillMock);

        $playerSkillRepoMock = $this->createMock(ObjectRepository::class);
        $playerSkillRepoMock->method('findBy')->willReturn([$playersSkillMock]);

        $joueurRepoMock = $this->getMockBuilder(Players::class)
            ->setMethods(['listeDesJoueursPourlEquipe'])
            ->getMock();
        $joueurRepoMock->method('listeDesJoueursPourlEquipe')->willReturn([$joueurMock]);

        $objectManager = $this->createMock(EntityManagerInterface::class);
        $objectManager->method('getRepository')->will($this->returnCallback(
            function ($entityName) use ($joueurRepoMock, $playerSkillRepoMock) {
                if ($entityName === 'App\Entity\Players') {
                    return $joueurRepoMock;
                }

                if ($entityName === 'App\Entity\PlayersSkills') {
                    return $playerSkillRepoMock;
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

        $this->assertEquals('130000', $playerService->coutTotalJoueurs($equipeMock));
    }
}