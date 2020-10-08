<?php


namespace App\Tests\src\Controller\EquipeController;


use App\DataFixtures\CitationFixture;
use App\DataFixtures\CoachesFixture;
use App\DataFixtures\DykFixture;
use App\DataFixtures\SettingFixtures;
use App\Tests\src\Functionnal;

class montreLesEquipesTest extends Functionnal
{
    /**
     * @test
     */
    public function montre_les_equipes()
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

        $this->client->request('GET', '/montreLesEquipes');

        $this->assertResponseIsSuccessful();
    }
}