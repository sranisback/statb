<?php


namespace App\Tests\src\Controller\EquipeController;

use App\DataFixtures\CoachesFixture;
use App\DataFixtures\SettingFixtures;
use App\Tests\src\Functionnal;

class showuserteamsTest extends Functionnal
{
    /**
     * @test
     */
    public function equipes_montrees()
    {
        $settingFixture = new SettingFixtures();
        $settingFixture->setReferenceRepository($this->referenceRepo);
        $settingFixture->load($this->entityManager);

        $settingFixture = new SettingFixtures();
        $settingTest = $settingFixture->load($this->entityManager);
        $settingTest->setName('currentRuleset');
        $settingTest->setValue(0);

        $coachFixture = new CoachesFixture();
        $coachFixture->setReferenceRepository($this->referenceRepo);
        $this->client->loginUser($coachFixture->load($this->entityManager));
        
        $this->client->request('GET', '/showuserteams');

        $this->assertResponseIsSuccessful();
        $this->assertStringContainsString('equipesEnCours', $this->client->getResponse());
    }
}
