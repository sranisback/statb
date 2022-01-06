<?php


namespace App\Tests\src;

use App\Controller\StatBBController;
use App\DataFixtures\SmokeFixture;
use App\Entity\Coaches;
use App\Entity\Players;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\Executor\ORMExecutor;
use Doctrine\Common\DataFixtures\Loader;
use Doctrine\Common\DataFixtures\Purger\ORMPurger;
use Doctrine\Common\DataFixtures\ReferenceRepository;
use Doctrine\ORM\Tools\SchemaTool;
use Doctrine\ORM\Tools\ToolsException;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class SmokeTest extends WebTestCase
{
    /**
     * @var KernelBrowser
     */
    public KernelBrowser $client;

    public $entityManager;

    /**
     * @var ReferenceRepository
     */
    public ReferenceRepository $referenceRepo;

    public function setUp(): void
    {
        parent::setUp();

        $this->client = static::createClient();
        $container = $this->client->getContainer();
        $doctrine = $container->get('doctrine');
        $this->entityManager = $doctrine->getManager();

        $schemaTool = new SchemaTool($this->entityManager);
        $metadata = $this->entityManager->getMetadataFactory()->getAllMetadata();
        $schemaTool->dropSchema($metadata);
        try {
            $schemaTool->createSchema($metadata);
        } catch (ToolsException $e) {
            echo 'impossible de creer le schema';
        }

        $this->referenceRepo = new ReferenceRepository($this->entityManager);

        $this->loadFixtures(
            [
                SmokeFixture::class
            ]
        );

        $this->client->loginUser($doctrine->getRepository(Coaches::class)->findBy(['coachId' => 1])[0]);
    }

    /**
     * @dataProvider urlProvider
     * @param String $url
     */
    public function testPageIsSuccessful(String $url)
    {
        $this->client->followRedirects(true);
        $this->client->request('GET', $url);

        $this->assertResponseIsSuccessful();
    }

    /**
     * @dataProvider urlProviderPOST
     * @param String $url
     * @param array $parametres
     */
    public function testPageIsSuccessfulPost(String $url, Array $parametres)
    {
        $this->client->request('POST', $url, $parametres);

        $this->assertResponseIsSuccessful();
    }

    public function urlProviderPOST(): \Generator
    {
        yield ['/Admin/coaches/updateEditableCoach', ['pk' => 1, 'name' => 'Username', 'value' => 'test']];
        yield ['/Admin/defis/updateEditableDefis', ['pk' => 1, 'name' => 'DefieRealise', 'value' => true]];
        yield ['/Admin/match/data/updateEditableMatchData', ['pk' => 1, 'name' => 'Mvp', 'value' => 5]];
        yield ['/Admin/matches/updateEditableMatch', ['pk' => 1, 'name' => 'Fans', 'value' => 1000]];
        yield ['/Admin/players/updateEditablePlayers', ['pk' => 1, 'name' => 'Nr', 'value' => 25]];
        yield ['/Admin/teams/updateEditableTeams', ['pk' => 1, 'name' => 'Treasury', 'value' => 50000]];
        yield ['/changeNomStade', ['pk' => 1, 'name' => 'Nom', 'value' => 'toto']];

        // ne fait rien
        yield ['/Admin/historique/blessure/updateEditableHisto', ['pk' => 1, 'name' => 'Player', 'value' =>  StatBBController::transformeObjetEnJsonResponse(new Players())->getContent()]];

        // a retravailler
        //yield ['/pdfTournois',StatBBController::transformeObjetEnJsonResponse(['post' => ['test', 'test']])];

        //trouver comment tester
        //yield ['/getposstat/1',[$this->createMock(EquipeService::class),[]]];
        //yield ['/addPlayer'];
        //yield ['/changeNomEtNumero'];
        //yield ['/genereNumero'];
        //yield ['/ajoutComp/1'];
        //yield ['/addGame'];
    }

    public function urlProviderDELETE(): \Generator
    {
        yield ['/Admin/coaches/1'];
        yield ['/Admin/defis/1'];
        yield ['/Admin/historique/blessure/1'];
        yield ['/Admin/match/data/1'];
        yield ['/Admin/matches/1'];
        yield ['/Admin/players/1'];
        yield ['/Admin/players/skills/1'];
        yield ['/Admin/teams/1'];
    }

    public function urlProviderRedirect(): \Generator
    {
        yield ['/calculClassementGen/6'];
        yield ['/supprimerDefis/1'];
        yield ['/team/test'];
        yield ['/retireEquipe/1'];
        yield ['/checkteam/1'];
        yield ['/recalculerTV'];
        yield ['/mettreEnFranchise/1'];
        yield ['/supprimerPrime/1'];
        yield ['/logout'];
    }

    public function urlProvider(): \Generator
    {
        yield ['/calculClassementGen/6'];
        yield ['/supprimerDefis/1'];
        yield ['/team/test'];
        yield ['/retireEquipe/1'];
        yield ['/checkteam/1'];
        yield ['/recalculerTV'];
        yield ['/mettreEnFranchise/1'];
        yield ['/supprimerPrime/1'];
        yield ['/logout'];
        //yield ['/pdfTeam/1']; - erreur stream
     /*   yield ['/Admin'];
        yield ['/Admin/coaches/'];
        yield ['/Admin/coaches/new'];
        yield ['/Admin/coaches/1/edit'];
        yield ['/Admin/defis/'];
        yield ['/Admin/defis/new'];
        yield ['/Admin/defis/1/edit'];
        yield ['/Admin/historique/blessure/'];
        yield ['/Admin/historique/blessure/new'];
        yield ['/Admin/historique/blessure/1/edit'];
        yield ['/Admin/match/data/'];
        yield ['/Admin/match/data/new'];
        yield ['/Admin/match/data/1/edit'];
        yield ['/Admin/matches/'];
        yield ['/Admin/matches/new'];
        yield ['/Admin/matches/1/edit'];
        yield ['/Admin/players/'];
        yield ['/Admin/players/new'];
        yield ['/Admin/players/1/edit'];
        yield ['/Admin/players/skills/'];
        yield ['/Admin/players/skills/new'];
        yield ['/Admin/players/skills/1/edit'];
        yield ['/Admin/teams/'];
        yield ['/Admin/teams/new'];
        yield ['/Admin/teams/1/edit'];
        yield ['/classement/general/6/test'];
        yield ['/classement/detail/6'];
        yield ['/classementEquipe/bash/5/6'];
        yield ['/classementJoueur/bash/5/6'];
        yield ['/totalcas/6'];
        yield ['/cinqDernierMatch'];
        yield ['/cinqDernierMatchPourEquipe/1'];
        yield ['/tousLesMatchesPourEquipe/1'];
        yield ['/montreLeCimetierre'];
        yield ['/montreClassementELO'];
        yield ['/montreConfrontation'];
        yield ['/ancienClassement/6'];
        yield ['/listeAnciennesAnnees'];
        yield ['/matchesContreCoach/1'];
        yield ['/ajoutDefisForm/'];
        yield ['/afficherDefis'];
        yield ['/afficherPeriodeDefisActuelle'];
        yield ['/montreLesEquipes'];
        yield ['/montreLesAnciennesEquipes'];
        yield ['/showuserteams'];
        yield ['/team/1'];
        yield ['/uploadLogo/1'];
        yield ['/createTeam'];
        yield ['/gestionInducement/add/1/rr'];
        yield ['/ajoutStadeModal/1'];
        yield ['/listeDesJoueurs/1'];
        yield ['/supprimeLogo/1'];
        yield ['/infos'];
        yield ['/infosCompletes'];
        yield ['/player/1'];
        yield ['/playerAdder/1'];
        yield ['/remPlayer/1'];
        yield ['/skillmodal/1'];
        yield ['/genereNom'];
        yield ['/uploadPhoto/1'];
        yield ['/supprimePhoto/1'];
        yield ['/dropdownPlayer/1/1'];
        yield ['/ajoutMatch'];
        yield ['/match/1'];
        yield ['/anciensMatchs'];
        yield ['/matchsAnnee'];
        yield ['/ajoutPenaliteForm'];
        yield ['/afficherPenalite'];
        yield ['/ajoutPrimeForm'];
        yield ['/montrePrimesEnCours'];
        yield ['/realiserPrimeForm'];
        yield ['/'];
        yield ['/login'];
        yield ['/citation'];
        yield ['/dyk'];
        yield ['/frontUser'];
        yield ['/tabCoach'];
        yield ['/tabLigue'];
        yield ['/testIcons'];
        yield ['/attributIconManquante'];
        yield ['/genereNomManquant'];
        yield ['/majBaseSkill'];
        yield ['/tournois'];
        yield ['/listePosition/1'];
        yield ['/nombreVersComp/1'];
        yield ['/classeLesComp/G/A'];
        yield ['/usercontrol'];*/
    }

    public function tearDown(): void
    {
        parent::tearDown();

        $purger = new ORMPurger($this->entityManager);
        $purger->setPurgeMode(ORMPurger::PURGE_MODE_DELETE);
        $purger->purge();
    }

    private $loader;

    public function loadFixtures(array $classNames):void
    {
        $this->loader = new Loader();

        foreach ($classNames as $className) {
            $this->loader->addFixture(new $className());
        }

        $executor = new ORMExecutor($this->entityManager, new ORMPurger());
        $executor->execute($this->loader->getFixtures());
    }

    public function getFixture(string $className) : Fixture
    {
        if ($this->loader == null) {
            echo 'The fixture %s must be loaded before you can access it.';
        }

        return $this->loader->getFixture($className);
    }
}