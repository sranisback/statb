<?php

namespace App\Tests\src\Service\PlayerService;


use App\Entity\Players;
use App\Entity\Races;
use App\Entity\Stades;
use App\Entity\Teams;
use App\Service\PlayerService;
use Doctrine\Common\Persistence\ObjectRepository;
use Doctrine\ORM\EntityManagerInterface;
use PHPUnit\Framework\TestCase;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class listeDesJoueursActifsDelEquipeTest extends KernelTestCase
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

        $joueur0 = new Players;
        $joueur0->setStatus(1);
        $joueur0->setOwnedByTeam($equipe);

        $joueur1 = new Players;
        $joueur1->setStatus(1);
        $joueur1->setOwnedByTeam($equipe);

        $joueur2 = new Players;
        $joueur2->setStatus(9);
        $joueur2->setOwnedByTeam($equipe);

        $joueur3 = new Players;
        $joueur3->setStatus(7);
        $joueur3->setOwnedByTeam($equipe);

        $joueur4 = new Players;
        $joueur4->setStatus(8);
        $joueur4->setOwnedByTeam($equipe);


        $this->entityManager->persist($joueur0);
        $this->entityManager->persist($joueur1);
        $this->entityManager->persist($joueur2);
        $this->entityManager->persist($joueur3);
        $this->entityManager->persist($joueur4);

        $this->entityManager->flush();
    }

    /**
     * @test
     */
    public function Le_compte_du_joueur_est_bon()
    {
        $this->assertEquals(
            3,
            count(
                $this->entityManager->getRepository(Players::class)->listeDesJoueursActifsPourlEquipe(
                    $this->entityManager->getRepository(Teams::class)->findOneBy(['name' => 'test EquipeListeActif'])
                )
            )
        );
    }

    public function tearDown()
    {
        $equipe = $this->entityManager->getRepository(Teams::class)->findOneBy(['name' => 'test EquipeListeActif']);

        foreach ($this->entityManager->getRepository(Players::class)->findBy(['ownedByTeam' => $equipe]) as $joueur) {
            $this->entityManager->remove($joueur);
        }

        $this->entityManager->remove($equipe);
        $this->entityManager->flush();
    }
}