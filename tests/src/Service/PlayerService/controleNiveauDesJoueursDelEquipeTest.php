<?php

namespace App\Tests\src\Service\PlayerService;

use App\Entity\MatchData;
use App\Entity\Players;
use App\Entity\PlayersSkills;
use App\Entity\Teams;
use App\Enum\RulesetEnum;
use App\Service\EquipeGestionService;
use App\Service\EquipeService;
use App\Service\InfosService;
use App\Service\MatchDataService;
use App\Service\PlayerService;
use Doctrine\Persistence\ObjectRepository;
use Doctrine\ORM\EntityManagerInterface;
use PHPUnit\Framework\TestCase;

class controleNiveauDesJoueursDelEquipeTest extends TestCase
{
    /**
     * @test
     */
    public function le_controle_est_bien_fait_sur_l_exp_bb2016(): void
    {
        $equipeMock = $this->createMock(Teams::class);
        $equipeMock->method('getRuleset')->willReturn(RulesetEnum::BB_2016);

        $joueur = new Players();
        $joueur->setStatus(1);
        $joueur->setRuleset(RulesetEnum::BB_2016);

        $joueurRepoMock = $this->getMockBuilder(Players::class)
            ->addMethods(['listeDesJoueursActifsPourlEquipe'])
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
                if ($entityName === Players::class) {
                    return $joueurRepoMock;
                }

                if ($entityName === MatchData::class) {
                    return $matchDataRepoMock;
                }

                if ($entityName === PlayersSkills::class) {
                    return $playerSkillRepoMock;
                }
                return true;
            }
        ));

        $matchDataService = new MatchDataService($objectManager);

        $playerService = new PlayerService(
            $objectManager,
            $this->createMock(EquipeGestionService::class),
            $matchDataService,
            $this->createMock(InfosService::class)
        );

        $playerService->controleNiveauDesJoueursDelEquipe($equipeMock);

        $this->assertEquals(9, $joueur->getStatus());
    }

    /**
     * @test
     */
    public function le_controle_est_bien_fait_sur_l_exp_bb2020(): void
    {
        $equipeMock = $this->createMock(Teams::class);
        $equipeMock->method('getRuleset')->willReturn(RulesetEnum::BB_2020);

        $joueur = new Players();
        $joueur->setStatus(1);
        $joueur->setRuleset(RulesetEnum::BB_2020);
        $joueur->setSppDepense(4);

        $joueurRepoMock = $this->getMockBuilder(Players::class)
            ->addMethods(['listeDesJoueursActifsPourlEquipe'])
            ->getMock();
        $joueurRepoMock->method('listeDesJoueursActifsPourlEquipe')->willReturn([$joueur]);

        $matchData = new MatchData();
        $matchData->setMvp(1);
        $matchData->setBh(1);
        $matchData->setDet(1);

        $matchDataRepoMock = $this->createMock(ObjectRepository::class);
        $matchDataRepoMock->method('findBy')->willReturn([$matchData]);

        $playerSkillRepoMock = $this->createMock(ObjectRepository::class);
        $playerSkillRepoMock->method('findBy')->willReturn([]);

        $objectManager = $this->createMock(EntityManagerInterface::class);
        $objectManager->method('getRepository')->will($this->returnCallback(
            function ($entityName) use ($joueurRepoMock, $matchDataRepoMock, $playerSkillRepoMock) {
                if ($entityName === Players::class) {
                    return $joueurRepoMock;
                }

                if ($entityName === MatchData::class) {
                    return $matchDataRepoMock;
                }

                if ($entityName === PlayersSkills::class) {
                    return $playerSkillRepoMock;
                }
                return true;
            }
        ));

        $playerService = new PlayerService(
            $objectManager,
            $this->createMock(EquipeGestionService::class),
            $this->createMock(MatchDataService::class),
            $this->createMock(InfosService::class)
        );

        $playerService->controleNiveauDesJoueursDelEquipe($equipeMock);

        $this->assertEquals(9, $joueur->getStatus());
    }

    /**
     * @test
     */
    public function pas_assez_d_xp_pour_le_nbr_de_comp_bb2016(): void
    {
        $equipeMock = $this->createMock(Teams::class);
        $equipeMock->method('getRuleset')->willReturn(RulesetEnum::BB_2016);

        $joueur = new Players();
        $joueur->setStatus(1);
        $joueur->setRuleset(RulesetEnum::BB_2016);

        $joueurRepoMock = $this->getMockBuilder(Players::class)
            ->addMethods(['listeDesJoueursActifsPourlEquipe'])
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
                if ($entityName === Players::class) {
                    return $joueurRepoMock;
                }

                if ($entityName === MatchData::class) {
                    return $matchDataRepoMock;
                }

                if ($entityName === PlayersSkills::class) {
                    return $playerSkillRepoMock;
                }
                return true;
            }
        ));

        $matchDataService = new MatchDataService($objectManager);

        $playerService = new PlayerService(
            $objectManager,
            $this->createMock(EquipeGestionService::class),
            $matchDataService,
            $this->createMock(InfosService::class)
        );

        $playerService->controleNiveauDesJoueursDelEquipe($equipeMock);

        $this->assertEquals(1, $joueur->getStatus());
    }

    /**
     * @test
     */
    public function pas_assez_d_xp_pour_une_nouvelle_comp_bb2020(): void
    {
        $equipeMock = $this->createMock(Teams::class);
        $equipeMock->method('getRuleset')->willReturn(RulesetEnum::BB_2020);

        $joueur = new Players();
        $joueur->setStatus(1);
        $joueur->setRuleset(RulesetEnum::BB_2020);
        $joueur->setSppDepense(5);

        $joueurRepoMock = $this->getMockBuilder(Players::class)
            ->addMethods(['listeDesJoueursActifsPourlEquipe'])
            ->getMock();
        $joueurRepoMock->method('listeDesJoueursActifsPourlEquipe')->willReturn([$joueur]);

        $matchData = new MatchData();
        $matchData->setMvp(1);
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
                if ($entityName === Players::class) {
                    return $joueurRepoMock;
                }

                if ($entityName === MatchData::class) {
                    return $matchDataRepoMock;
                }

                if ($entityName === PlayersSkills::class) {
                    return $playerSkillRepoMock;
                }
                return true;
            }
        ));

        $playerService = new PlayerService(
            $objectManager,
            $this->createMock(EquipeGestionService::class),
            $this->createMock(MatchDataService::class),
            $this->createMock(InfosService::class)
        );

        $playerService->controleNiveauDesJoueursDelEquipe($equipeMock);

        $this->assertEquals(1, $joueur->getStatus());
    }
}