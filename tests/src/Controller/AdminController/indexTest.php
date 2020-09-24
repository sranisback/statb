<?php


namespace App\Tests\src\Controller\AdminController;

use App\DataFixtures\CitationFixture;
use App\DataFixtures\CoachesFixture;
use App\DataFixtures\DykFixture;
use App\DataFixtures\SettingFixtures;
use App\Tests\src\Functionnal;

class indexTest extends Functionnal
{
    private $settingFixture;
    private $dykFixture;
    private $coachFixture;
    private $citationFixture;

    public function setUp(): void
    {
        parent::setUp();

        $this->settingFixture = new SettingFixtures();
        $this->settingFixture->load($this->entityManager);

        $this->dykFixture = new DykFixture();
        $this->dykFixture->load($this->entityManager);

        $this->coachFixture = new CoachesFixture();
        $this->coachFixture->setReferenceRepository($this->referenceRepo);
        $this->coachFixture->load($this->entityManager);

        $this->citationFixture = new CitationFixture();
        $this->citationFixture->setReferenceRepository($this->referenceRepo);
        $this->citationFixture->load($this->entityManager);
    }

    /**
     * @test
     */
    public function l_admin_s_affiche_correctement()
    {
        $this->client->request('GET', '/Admin');

        $this->assertResponseIsSuccessful();
        $this->assertSelectorTextContains('h1', 'admin');
    }
}
