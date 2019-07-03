<?php

namespace App\Tests\src\Service\PlayerService;

use App\Entity\GameDataPlayers;
use App\Entity\MatchData;
use App\Entity\Matches;
use App\Entity\Players;
use App\Entity\Teams;
use App\Service\EquipeService;
use App\Service\MatchDataService;
use App\Service\PlayerService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class toutesLesActionsDeLequipeDansUnMatchTest extends KernelTestCase
{
    /**
     * @test
     */
    public function les_actions_des_joueurs_sont_retournees()
    {
        $position = new GameDataPlayers();
        $position->setPos('Witch Elf');
        $equipe = new Teams();

        $joueur = new Players();
        $joueur->setOwnedByTeam($equipe);
        $joueur->setFPos($position);
        $joueur->setName('joueur test');
        $joueur->setNr(1);

        $match = new Matches();

        $matchData = new MatchData();
        $matchData->setFMatch($match);
        $matchData->setFPlayer($joueur);
        $matchData->setMvp(1);

        $MatchDataRepoMock = $this->getMockBuilder(MatchData::class)
            ->setMethods(['listeDesJoueursdUnMatch', 'findBy'])
            ->getMock();
        $MatchDataRepoMock->method('listeDesJoueursdUnMatch')->willReturn([$matchData]);
        $MatchDataRepoMock->method('findBy')->willReturn([$matchData]);

        $objectManager = $this->createMock(EntityManagerInterface::class);
        $objectManager->method('getRepository')->willReturn($MatchDataRepoMock);

        $matchDataService = new MatchDataService($objectManager);

        $playerService = new PlayerService(
            $objectManager,
            $this->createMock(EquipeService::class),
            $matchDataService
        );

        $this->assertEquals(
            '<ul><li>joueur test, Witch Elf(1): MVP: 1</li></ul>',
            $playerService->toutesLesActionsDeLequipeDansUnMatch($match, $equipe)
        );
    }

    /**
     * @test
     */
    public function le_joueur_sans_nom_retourne_inconnu()
    {
        $position = new GameDataPlayers();
        $position->setPos('Witch Elf');
        $equipe = new Teams();

        $joueur = new Players();
        $joueur->setOwnedByTeam($equipe);
        $joueur->setFPos($position);
        $joueur->setNr(1);

        $match = new Matches();

        $matchData = new MatchData();
        $matchData->setFMatch($match);
        $matchData->setFPlayer($joueur);
        $matchData->setMvp(1);

        $MatchDataRepoMock = $this->getMockBuilder(MatchData::class)
            ->setMethods(['listeDesJoueursdUnMatch', 'findBy'])
            ->getMock();
        $MatchDataRepoMock->method('listeDesJoueursdUnMatch')->willReturn([$matchData]);
        $MatchDataRepoMock->method('findBy')->willReturn([$matchData]);

        $objectManager = $this->createMock(EntityManagerInterface::class);
        $objectManager->method('getRepository')->willReturn($MatchDataRepoMock);

        $matchDataService = new MatchDataService($objectManager);

        $playerService = new PlayerService(
            $objectManager,
            $this->createMock(EquipeService::class),
            $matchDataService
        );

        $this->assertEquals(
            '<ul><li>Inconnu, Witch Elf(1): MVP: 1</li></ul>',
            $playerService->toutesLesActionsDeLequipeDansUnMatch($match, $equipe)
        );
    }

    /**
     * @test
     */
    public function le_joueur_n_est_pas_retourne_s_il_n_a_rien_fait()
    {
        $position = new GameDataPlayers();
        $position->setPos('Witch Elf');
        $equipe = new Teams();

        $joueur = new Players();
        $joueur->setOwnedByTeam($equipe);
        $joueur->setFPos($position);
        $joueur->setNr(1);

        $match = new Matches();

        $matchData = new MatchData();
        $matchData->setFMatch($match);
        $matchData->setFPlayer($joueur);

        $MatchDataRepoMock = $this->getMockBuilder(MatchData::class)
            ->setMethods(['listeDesJoueursdUnMatch', 'findBy'])
            ->getMock();
        $MatchDataRepoMock->method('listeDesJoueursdUnMatch')->willReturn([$matchData]);
        $MatchDataRepoMock->method('findBy')->willReturn([$matchData]);

        $objectManager = $this->createMock(EntityManagerInterface::class);
        $objectManager->method('getRepository')->willReturn($MatchDataRepoMock);

        $matchDataService = new MatchDataService($objectManager);

        $playerService = new PlayerService(
            $objectManager,
            $this->createMock(EquipeService::class),
            $matchDataService
        );

        $this->assertEquals('<ul></ul>', $playerService->toutesLesActionsDeLequipeDansUnMatch($match, $equipe));
    }
}