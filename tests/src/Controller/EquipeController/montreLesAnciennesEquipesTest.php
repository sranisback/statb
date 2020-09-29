<?php


namespace App\Tests\src\Controller\EquipeController;


use App\DataFixtures\CoachesFixture;
use App\Tests\src\Functionnal;

class montreLesAnciennesEquipesTest extends Functionnal
{
    /**
     * @test
     */
    public function montre_les_anciennes_equipes()
    {
        $coachFixture = new CoachesFixture();
        $coachFixture->setReferenceRepository($this->referenceRepo);
        $this->client->loginUser($coachFixture->load($this->entityManager));

        $this->client->request('GET', '/montreLesAnciennesEquipes');

        $this->assertStringContainsString('TableAnciennesEquipes', $this->client->getResponse());
        $this->assertResponseIsSuccessful();
    }

}