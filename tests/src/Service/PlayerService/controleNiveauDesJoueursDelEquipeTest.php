<?php

namespace App\Tests\src\Service\PlayerService;

use App\Entity\MatchData;
use App\Entity\Players;
use App\Entity\PlayersSkills;
use App\Entity\Teams;
use App\Service\EquipeService;
use App\Service\MatchDataService;
use App\Service\PlayerService;
use Doctrine\Common\Persistence\ObjectRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class controleNiveauDesJoueursDelEquipeTest extends KernelTestCase
{
    /**
     * @test
     */
    public function le_controle_est_bien_fait_sur_l_exp()
    {
        $equipeMock = $this->createMock(Teams::class);

        $joueur = new Players();
        $joueur->setStatus(1);

        $joueurRepoMock = $this->getMockBuilder(Players::class)
            ->setMethods(['listeDesJoueursActifsPourlEquipe'])
            ->getMock();
        $joueurRepoMock->method('listeDesJoueursActifsPourlEquipe')->willReturn([$joueur]);

        $matchData = new MatchData();
        $matchData->setMvp(1);
        $matchData->setBh(1);

        $matchDataRepoMock = $this->createMock(ObjectRepository::class);
        $matchDataRepoMock->method('findBy')->willReturn([$matchData]);

        $playerSkillRepoMock = $this->createMock(ObjectRepository::class);
        $playerSkillRepoMock->method('findBy')->willReturn([]);

        $objectManager = $this->createMock(EntityManagerInterface::class);
        $objectManager->method('getRepository')->will($this->returnCallback(
            function ($entityName) use ($joueurRepoMock, $matchDataRepoMock, $playerSkillRepoMock) {
                if ($entityName === 'App\Entity\Players') {
                    return $joueurRepoMock;
                }

                if ($entityName === 'App\Entity\MatchData') {
                    return $matchDataRepoMock;
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

        $playerService->controleNiveauDesJoueursDelEquipe($equipeMock);

        $this->assertEquals(9, $joueur->getStatus());
    }

    /**
     * @test
     */
    public function pas_assez_d_xp_pour_le_nbr_de_comp()
    {
        $equipeMock = $this->createMock(Teams::class);

        $joueur = new Players();
        $joueur->setStatus(1);

        $joueurRepoMock = $this->getMockBuilder(Players::class)
            ->setMethods(['listeDesJoueursActifsPourlEquipe'])
            ->getMock();
        $joueurRepoMock->method('listeDesJoueursActifsPourlEquipe')->willReturn([$joueur]);

        $matchData = new MatchData();
        $matchData->setBh(1);

        $matchDataRepoMock = $this->createMock(ObjectRepository::class);
        $matchDataRepoMock->method('findBy')->willReturn([$matchData]);

        $playerSkillMok0 = $this->createMock(PlayersSkills::class);
        $playerSkillMok1 = $this->createMock(PlayersSkills::class);

        $playerSkillRepoMock = $this->createMock(ObjectRepository::class);
        $playerSkillRepoMock->method('findBy')->willReturn([$playerSkillMok0,$playerSkillMok1]);

        $objectManager = $this->createMock(EntityManagerInterface::class);
        $objectManager->method('getRepository')->will($this->returnCallback(
            function ($entityName) use ($joueurRepoMock, $matchDataRepoMock, $playerSkillRepoMock) {
                if ($entityName === 'App\Entity\Players') {
                    return $joueurRepoMock;
                }

                if ($entityName === 'App\Entity\MatchData') {
                    return $matchDataRepoMock;
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

        $playerService->controleNiveauDesJoueursDelEquipe($equipeMock);

        $this->assertEquals(1, $joueur->getStatus());
    }
}