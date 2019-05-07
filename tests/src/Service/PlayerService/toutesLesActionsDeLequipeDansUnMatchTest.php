<?php

namespace App\Tests\src\Service\PlayerService;


use App\Entity\GameDataPlayers;
use App\Entity\GameDataStadium;
use App\Entity\MatchData;
use App\Entity\Matches;
use App\Entity\Meteo;
use App\Entity\Players;
use App\Entity\Races;
use App\Entity\Stades;
use App\Entity\Teams;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class toutesLesActionsDeLequipeDansUnMatchTest extends KernelTestCase
{
    private $entityManager;

    protected function setUp()
    {
        self::bootKernel();
        $container = self::$container;

        $this->entityManager = $container
            ->get('doctrine')
            ->getManager();

        $equipe1 = new Teams;

        $equipe1->setFRace($this->entityManager->getRepository(Races::class)->findOneBy(['raceId' => 9]));
        $equipe1->setName('test EquipeAi0');
        $equipe1->setYear(3);
        $equipe1->setFStades($this->entityManager->getRepository(Stades::class)->findOneBy(['id' => 0]));
        $equipe1->setTreasury(500000);

        $this->entityManager->persist($equipe1);

        $joueur = new Players;

        $joueur->setName('joueur test');
        $joueur->setOwnedByTeam($equipe1);
        $joueur->setNr(1);
        $joueur->setFPos($this->entityManager->getRepository(GameDataPlayers::class)->findOneBy(['posId' => 34]));

        $this->entityManager->persist($joueur);

        $match = new Matches;
        $match->setFMeteo($this->entityManager->getRepository(Meteo::class)->findOneBy(['id'=> 0]));
        $match->setFStade($this->entityManager->getRepository(GameDataStadium::class)->findOneBy(['id'=> 0]));

        $this->entityManager->persist($match);

        $matchData = new MatchData;

        $matchData->setFPlayer($joueur);
        $matchData->setFMatch($match);
        $matchData->setMvp(1);

        $this->entityManager->persist($matchData);

        $this->entityManager->flush();
    }

    /**
 * @test
 */
    public function les_actions_des_joueurs_sont_retournees()
    {
        $playerService = self::$container->get('App\Service\PlayerService');

        $joueur = $this->entityManager->getRepository(Players::class)->findOneBy(['name' => 'joueur test']);

        $matchData = $this->entityManager->getRepository(MatchData::class)->findOneBy(['fPlayer'=>$joueur]);

        /** @var MatchData $matchData */
        $match = $matchData->getFMatch();

        $this->assertEquals('<ul><li>joueur test, Witch Elf(1): MVP: 1</li></ul>',$playerService->toutesLesActionsDeLequipeDansUnMatch($match, $this->entityManager->getRepository(Teams::class)->findOneBy(['name' => 'test EquipeAi0'])));
    }

    /**
     * @test
     */
    public function le_joueur_sans_nom_retourne_inconnu()
    {
        $playerService = self::$container->get('App\Service\PlayerService');

        /** @var Players $joueur */
        $joueur = $this->entityManager->getRepository(Players::class)->findOneBy(['name' => 'joueur test']);

        $joueur->setName('');

        $matchData = $this->entityManager->getRepository(MatchData::class)->findOneBy(['fPlayer'=>$joueur]);

        /** @var MatchData $matchData */
        $match = $matchData->getFMatch();

        $this->assertEquals('<ul><li>Inconnu, Witch Elf(1): MVP: 1</li></ul>',$playerService->toutesLesActionsDeLequipeDansUnMatch($match, $this->entityManager->getRepository(Teams::class)->findOneBy(['name' => 'test EquipeAi0'])));
    }

    /**
     * @test
     */
    public function le_joueur_n_est_pas_retourne_s_il_n_a_rien_fait()
    {
        $playerService = self::$container->get('App\Service\PlayerService');

        $joueur = $this->entityManager->getRepository(Players::class)->findOneBy(['name' => 'joueur test']);

        $matchData = $this->entityManager->getRepository(MatchData::class)->findOneBy(['fPlayer'=>$joueur]);

        /** @var MatchData $matchData */
        $match = $matchData->getFMatch();

        $matchData->setMvp(0);

        $this->entityManager->persist($matchData);

        $this->entityManager->flush();

        $this->assertEquals('<ul></ul>',$playerService->toutesLesActionsDeLequipeDansUnMatch($match, $this->entityManager->getRepository(Teams::class)->findOneBy(['name' => 'test EquipeAi0'])));

    }

    protected function tearDown()
    {
        $joueur = $this->entityManager->getRepository(Players::class)->findOneBy(['name' => 'joueur test']);

        $equipe1 = $this->entityManager->getRepository(Teams::class)->findOneBy(['name' => 'test EquipeAi0']);

        $matchData = $this->entityManager->getRepository(MatchData::class)->findOneBy(['fPlayer'=>$joueur]);

        /** @var MatchData $matchData */
        $match = $matchData->getFMatch();

        $this->entityManager->remove($matchData);

        $this->entityManager->remove($match);

        $this->entityManager->remove($joueur);

        $this->entityManager->remove($equipe1);

        $this->entityManager->flush();
    }
}