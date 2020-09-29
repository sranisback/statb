<?php


namespace App\Tests\src\Controller\EquipeController;

use App\DataFixtures\CitationFixture;
use App\DataFixtures\CoachesFixture;
use App\DataFixtures\DykFixture;
use App\DataFixtures\SettingFixtures;
use App\DataFixtures\TeamFixture;
use App\Tests\src\Functionnal;

class montreEquipeTest extends Functionnal
{
    public function setUp(): void
    {
        parent::setUp();

        $settingFixture = new SettingFixtures();
        $settingFixture->load($this->entityManager);

        $dykFixture = new DykFixture();
        $dykFixture->load($this->entityManager);

        $coachFixture = new CoachesFixture();
        $coachFixture->setReferenceRepository($this->referenceRepo);
        $coach = $coachFixture->load($this->entityManager);

        $citationFixture = new CitationFixture();
        $citationFixture->setReferenceRepository($this->referenceRepo);
        $citationFixture->load($this->entityManager);

        $teamFixture = new TeamFixture();
        $teamFixture->setReferenceRepository($this->referenceRepo);
        $equipe0 = $teamFixture->load($this->entityManager);
        $equipe0->setOwnedByCoach($coach);
        $equipe0->setName('test1');

        $equipe1 = $teamFixture->load($this->entityManager);
        $equipe1->setOwnedByCoach($coach);
        $equipe1->setName('test2');

        $this->entityManager->persist($equipe0);
        $this->entityManager->persist($equipe1);
        $this->entityManager->flush();

        $this->client->loginUser($coach);
        $this->client->followRedirects(true);
    }

    /**
     * @test
     */
    public function plusieurs_equipes_sont_trouvees()
    {
        $this->client->request('GET', '/team/te' );

        $this->assertResponseIsSuccessful();
        $this->assertStringContainsString('<h1>Voulez vous dire : </h1>', $this->client->getResponse());
    }

    /**
     * @test
     */
    public function une_seul_equipe_est_trouvee()
    {
        $this->client->request('GET', '/team/test1' );

        $this->assertResponseIsSuccessful();
        $this->assertStringContainsString('<strong>test1</strong>', $this->client->getResponse());
    }

    /**
     * @test
     */
    public function aucune_equipe_est_trouvee()
    {
        $this->client->request('GET', '/team/zatrg' );

        $this->assertResponseIsSuccessful();
        $this->assertStringContainsString('Classement général', $this->client->getResponse());
    }

    /**
     * @test
     */
    public function pas_de_string_en_entree() //bof bof
    {
        $this->client->request('GET', '/team/ ' );

        $this->assertResponseIsSuccessful();
        $this->assertStringContainsString('Classement général', $this->client->getResponse());
    }
}
