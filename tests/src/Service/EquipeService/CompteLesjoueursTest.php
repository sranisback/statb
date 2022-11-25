<?php


namespace App\Tests\src\Service\EquipeService;


use App\Entity\Players;
use App\Entity\Teams;
use App\Service\EquipeService;
use App\Tests\src\TestServiceFactory\EquipeServiceTestFactory;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\EntityManagerInterface;
use PHPUnit\Framework\TestCase;

class CompteLesjoueursTest extends TestCase
{

    private EquipeService $equipeService;

    public function setUp(): void
    {
        parent::setUp();

        $objectManager = $this->createMock(EntityManagerInterface::class);

        $this->equipeService = (new EquipeServiceTestFactory)->getInstance(
            $objectManager
        );
    }

    /**
     * @test
     */
    public function les_joueurs_sont_bien_comptes()
    {
        $joueur1 = New Players();

        $joueur2 = New Players();
        $joueur2->setJournalier(true);

        $joueur3 = New Players();
        $joueur3->setInjRpm(1);

        $joueursList = new ArrayCollection();
        $joueursList->add($joueur1);
        $joueursList->add($joueur2);
        $joueursList->add($joueur3);

        $equipe = new Teams();
        $equipe->setJoueurs($joueursList);

        $result = $this->equipeService->compteLesjoueurs($equipe);

        $this->assertEquals(1, $result['actif']);
        $this->assertEquals(1, $result['journalier']);
        $this->assertEquals(1, $result['blesses']);
    }

    /**
     * @test
     */
    public function il_n_y_a_pas_de_joueurs()
    {
        $equipe = new Teams();

        $result = $this->equipeService->compteLesjoueurs($equipe);

        $this->assertEquals(0, $result['actif']);
        $this->assertEquals(0, $result['journalier']);
        $this->assertEquals(0, $result['blesses']);
    }

    /**
     * @test
     */
    public function il_n_y_a_pas_de_blesses()
    {
        $joueur1 = New Players();

        $joueur2 = New Players();
        $joueur2->setJournalier(true);

        $joueursList = new ArrayCollection();
        $joueursList->add($joueur1);
        $joueursList->add($joueur2);

        $equipe = new Teams();
        $equipe->setJoueurs($joueursList);

        $result = $this->equipeService->compteLesjoueurs($equipe);

        $this->assertEquals(1, $result['actif']);
        $this->assertEquals(1, $result['journalier']);
        $this->assertEquals(0, $result['blesses']);
    }

    /**
     * @test
     */
    public function les_joueurs_sont_vendus_ou_morts()
    {
        $joueur1 = New Players();
        $joueur1->setStatus(7);

        $joueur2 = New Players();
        $joueur2->setStatus(8);

        $joueursList = new ArrayCollection();
        $joueursList->add($joueur1);
        $joueursList->add($joueur2);

        $equipe = new Teams();
        $equipe->setJoueurs($joueursList);

        $result = $this->equipeService->compteLesjoueurs($equipe);

        $this->assertEquals(0, $result['actif']);
        $this->assertEquals(0, $result['journalier']);
        $this->assertEquals(0, $result['blesses']);
    }
}