<?php


namespace App\Tests\src\Service\PlayerService;


use App\Entity\GameDataPlayers;
use App\Entity\GameDataSkills;
use App\Entity\Players;
use App\Entity\PlayersSkills;
use App\Entity\Races;
use App\Entity\Stades;
use App\Entity\Teams;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class coutTotalJoueursTest extends KernelTestCase
{
    private $entityManager;

    protected function setUp()
    {
        self::bootKernel();
        $container = self::$container;

        $this->entityManager = $container
            ->get('doctrine')
            ->getManager();

        $equipe = new Teams;
        $equipe->setYear(3);
        $equipe->setName('test EquipeListeActif');
        $equipe->setFRace($this->entityManager->getRepository(Races::class)->findOneBy(['raceId' => 9]));
        $equipe->setFStades($this->entityManager->getRepository(Stades::class)->findOneBy(['id' => 0]));

        $this->entityManager->persist($equipe);

        $joueur = new Players;

        $joueur->setFPos($this->entityManager->getRepository(GameDataPlayers::class)->findOneBy(['posId' => 34]));
        $joueur->setName('joueur test');
        $joueur->setType(1);
        $joueur->setOwnedByTeam($equipe);

        $dataSkill = $this->entityManager->getRepository(GameDataSkills::class)->findOneBy(['skillId'=>1]);

        $compSupp = new PlayersSkills;

        $compSupp->setFPid($joueur);
        $compSupp->setType('N');
        $compSupp->setFSkill($dataSkill);


        $this->entityManager->persist($joueur);
        $this->entityManager->persist($compSupp);

        $this->entityManager->flush();
    }

    /**
     * @test
     */
    public function le_cout_total_des_joueurs_est_bien_calcule()
    {
        $playerService = self::$container->get('App\Service\PlayerService');

        $this->assertEquals(130000,$playerService->coutTotalJoueurs($this->entityManager->getRepository(Teams::class)->findOneBy(['name' => 'test EquipeListeActif'])));
    }

    protected function tearDown()
    {
        self::bootKernel();
        $container = self::$container;

        $this->entityManager = $container
            ->get('doctrine')
            ->getManager();

        $equipe = $this->entityManager->getRepository(Teams::class)->findOneBy(['name' => 'test EquipeListeActif']);

        $joueur = $this->entityManager->getRepository(Players::class)->findOneBy(['name' => 'joueur test']);

        $this->entityManager->remove($this->entityManager->getRepository(PlayersSkills::class)->findOneBy(['fPid' => $joueur]));

        $this->entityManager->remove($joueur);
        $this->entityManager->remove($equipe);

        $this->entityManager->flush();
    }
}