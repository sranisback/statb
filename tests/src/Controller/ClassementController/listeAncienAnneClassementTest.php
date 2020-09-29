<?php


namespace App\Tests\src\Controller\ClassementController;

use App\DataFixtures\SettingFixtures;
use App\Tests\src\Functionnal;

class listeAncienAnneClassementTest extends Functionnal
{
    /**
     * @test
     */
    public function affiche_liste_ancien_classement()
    {
        $settingFixture = new SettingFixtures();
        $settingFixture->load($this->entityManager);

        $this->client->request('GET', '/listeAnciennesAnnees');

        $this->assertResponseIsSuccessful();
        $this->assertStringContainsString('2015 - 2016', $this->client->getResponse());
    }
}
