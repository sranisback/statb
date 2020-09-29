<?php


namespace App\Tests\src\Controller\AdminController;

use App\DataFixtures\CitationFixture;
use App\DataFixtures\CoachesFixture;
use App\DataFixtures\DykFixture;
use App\DataFixtures\SettingFixtures;
use App\Tests\src\Functionnal;

class indexTest extends Functionnal
{
    /**
     * @test
     */
    public function l_admin_s_affiche_correctement()
    {
        $settingFixture = new SettingFixtures();
        $settingFixture->load($this->entityManager);

        $dykFixture = new DykFixture();
        $dykFixture->load($this->entityManager);

        $coachFixture = new CoachesFixture();
        $coachFixture->setReferenceRepository($this->referenceRepo);
        $coachFixture->load($this->entityManager);

        $citationFixture = new CitationFixture();
        $citationFixture->setReferenceRepository($this->referenceRepo);
        $citationFixture->load($this->entityManager);

        $this->client->request('GET', '/Admin');

        $this->assertResponseIsSuccessful();
        $this->assertSelectorTextContains('h1', 'admin');
    }
}
