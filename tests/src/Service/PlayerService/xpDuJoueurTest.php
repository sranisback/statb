<?php

namespace App\Tests\src\Service\PlayerService;

use App\Entity\MatchData;
use App\Entity\Players;
use App\Enum\RulesetEnum;
use App\Tests\src\TestServiceFactory\PlayerServiceTestFactory;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Persistence\ObjectRepository;
use PHPUnit\Framework\TestCase;

class xpDuJoueurTest extends TestCase
{
    /**
     * @test
     */
    public function l_xp_totale_est_bien_calculee_bb2016(): void
    {
        $joueurTest = new Players();
        $joueurTest->setRuleset(RulesetEnum::BB_2016);

        $matchDataTest0 = new MatchData();
        $matchDataTest0->setMvp(1);
        $matchDataTest0->setCp(1);
        $matchDataTest0->setBh(1);
        $matchDataTest1 = new MatchData();
        $matchDataTest1->setTd(1);
        $matchDataTest1->setIntcpt(1);

        $matchDataRepoMock = $this->createMock(ObjectRepository::class);
        $matchDataRepoMock->method('findBy')->willReturn([$matchDataTest0, $matchDataTest1]);

        $objectManager = $this->createMock(EntityManagerInterface::class);
        $objectManager->method('getRepository')->willReturn($matchDataRepoMock);

        $playerServiceTest = (new PlayerServiceTestFactory())->getInstance(
            $objectManager
        );

        $this->assertEquals(13, $playerServiceTest->xpDuJoueur($joueurTest));
    }

    /**
     * @test
     */
    public function l_xp_totale_est_bien_calculee_bb2020(): void
    {
        $joueurTest = new Players();
        $joueurTest->setRuleset(RulesetEnum::BB_2020);

        $matchDataTest0 = new MatchData();
        $matchDataTest0->setMvp(1);
        $matchDataTest0->setCp(1);
        $matchDataTest0->setBh(1);
        $matchDataTest1 = new MatchData();
        $matchDataTest1->setTd(1);
        $matchDataTest1->setIntcpt(1);

        $matchDataRepoMock = $this->createMock(ObjectRepository::class);
        $matchDataRepoMock->method('findBy')->willReturn([$matchDataTest0, $matchDataTest1]);

        $objectManager = $this->createMock(EntityManagerInterface::class);
        $objectManager->method('getRepository')->willReturn($matchDataRepoMock);

        $playerServiceTest = (new PlayerServiceTestFactory())->getInstance(
            $objectManager
        );
        $this->assertEquals(12, $playerServiceTest->xpDuJoueur($joueurTest));
    }


    /**
     * @test
     */
    public function il_n_y_a_pas_de_donnees(): void
    {
        $joueurTest = new Players();

        $matchDataRepoMock = $this->createMock(ObjectRepository::class);
        $matchDataRepoMock->method('findBy')->willReturn([]);

        $objectManager = $this->createMock(EntityManagerInterface::class);
        $objectManager->method('getRepository')->willReturn($matchDataRepoMock);

        $playerServiceTest = (new PlayerServiceTestFactory())->getInstance(
            $objectManager
        );

        $this->assertEquals(0, $playerServiceTest->xpDuJoueur($joueurTest));
    }


    /**
     * @test
     */
    public function le_joueur_n_a_rien_fait_dans_les_matches(): void
    {
        $joueurTest = new Players();

        $matchDataTest0 = new MatchData();
        $matchDataTest1 = new MatchData();

        $matchDataRepoMock = $this->createMock(ObjectRepository::class);
        $matchDataRepoMock->method('findBy')->willReturn([$matchDataTest0, $matchDataTest1]);

        $objectManager = $this->createMock(EntityManagerInterface::class);
        $objectManager->method('getRepository')->willReturn($matchDataRepoMock);

        $playerServiceTest = (new PlayerServiceTestFactory())->getInstance(
            $objectManager
        );

        $this->assertEquals(0, $playerServiceTest->xpDuJoueur($joueurTest));
    }
}