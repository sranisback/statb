<?php


namespace App\Tests\src\Controller\EquipeController;


use App\DataFixtures\SettingFixtures;
use App\Tests\src\Functionnal;

class createTeamTest extends Functionnal
{
    /**
     * @test
     */
    public function le_form_s_affiche_bien()
    {
        $settingFixture = new SettingFixtures();
        $settingFixture->setReferenceRepository($this->referenceRepo);
        $settingFixture->load($this->entityManager);

        $settingFixture = new SettingFixtures();
        $settingTest = $settingFixture->load($this->entityManager);
        $settingTest->setName('currentRuleset');
        $settingTest->setValue(0);

        $this->entityManager->persist($settingTest);
        $this->entityManager->flush();

        $this->client->request('GET', '/createTeam' );

        $this->assertResponseIsSuccessful();
        $this->assertStringContainsString('Créer une équipe', $this->client->getResponse());
    }
}