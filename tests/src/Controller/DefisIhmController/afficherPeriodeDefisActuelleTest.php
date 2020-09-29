<?php


namespace App\Tests\src\Controller\DefisIhmController;


use App\DataFixtures\SettingFixtures;
use App\Tests\src\Functionnal;

class afficherPeriodeDefisActuelleTest extends Functionnal
{
    /**
     * @test
     */
    public function la_periode_est_retournee()
    {
        $settingFixture = new SettingFixtures();
        $settingFixture->load($this->entityManager);

        $settingTestPeriodeDefis = $settingFixture->load($this->entityManager);
        $settingTestPeriodeDefis->setName('periodeDefis');
        $settingTestPeriodeDefis->setValue('08/01/2020');
        $this->entityManager->persist($settingTestPeriodeDefis);
        $this->entityManager->flush();

        $this->client->request('GET', '/afficherPeriodeDefisActuelle');

        $this->assertResponseIsSuccessful();
        $this->assertStringContainsString('01/08/2020 - 01/10/2020', $this->client->getResponse());
    }

    /**
     * @test
     */
    public function pas_de_periode_defis_configuree()
    {
        $this->client->request('GET', '/afficherPeriodeDefisActuelle');

        $this->assertResponseIsSuccessful();
    }
}