<?php


namespace App\Tests\src\Controller\DefisIhmController;

use App\DataFixtures\CitationFixture;
use App\DataFixtures\CoachesFixture;
use App\DataFixtures\DykFixture;
use App\DataFixtures\SettingFixtures;
use App\Tests\src\Functionnal;

class afficherLesDefisTest extends Functionnal
{
    /**
     * @test
     */
    public function affichage_des_defis()
    {
        $dykFixture = new DykFixture();
        $dykFixture->load($this->entityManager);

        $coachFixture = new CoachesFixture();
        $coachFixture->setReferenceRepository($this->referenceRepo);
        $this->client->loginUser($coachFixture->load($this->entityManager));

        $citationFixture = new CitationFixture();
        $citationFixture->setReferenceRepository($this->referenceRepo);
        $citationFixture->load($this->entityManager);

        $settingFixture = new SettingFixtures();
        $settingFixture->load($this->entityManager);

        $settingTestPeriodeDefis = $settingFixture->load($this->entityManager);
        $settingTestPeriodeDefis->setName('periodeDefis');
        $settingTestPeriodeDefis->setValue('08/01/2020');
        $this->entityManager->persist($settingTestPeriodeDefis);

        $this->entityManager->flush();

        $this->client->request('GET', '/afficherDefis');

        $this->assertResponseIsSuccessful();
        $this->assertStringContainsString('TableDefis', $this->client->getResponse());
    }
}
