<?php


namespace App\Tests\src\Controller\ClassementController;

use App\DataFixtures\CitationFixture;
use App\DataFixtures\CoachesFixture;
use App\DataFixtures\DykFixture;
use App\DataFixtures\SettingFixtures;
use App\Tests\src\Functionnal;

class afficheAncienClassementTest extends Functionnal
{

    /**
     * @test
     */
    public function affiche_ancien_classement()
    {
        $coachFixture = new CoachesFixture();
        $coachFixture->setReferenceRepository($this->referenceRepo);
        $coachFixture->load($this->entityManager);

        $citationFixture = new CitationFixture();
        $citationFixture->setReferenceRepository($this->referenceRepo);
        $citationFixture->load($this->entityManager);

        $dykFixture = new DykFixture();
        $dykFixture->load($this->entityManager);

        $settingFixture = new SettingFixtures();
        $settingTest = $settingFixture->load($this->entityManager);
        $settingTest->setName('currentRuleset');
        $settingTest->setValue('0');

        $this->entityManager->persist($settingTest);
        $this->entityManager->flush();

        $this->client->request('GET', '/ancienClassement/3');

        $this->assertResponseIsSuccessful();
        $this->assertStringContainsString('Classement Général 2018 - 2019', $this->client->getResponse());
    }
}
