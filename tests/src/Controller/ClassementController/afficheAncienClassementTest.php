<?php


namespace App\Tests\src\Controller\ClassementController;

use App\DataFixtures\CitationFixture;
use App\DataFixtures\CoachesFixture;
use App\DataFixtures\DykFixture;
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

        $this->client->request('GET', '/ancienClassement/3');

        $this->assertResponseIsSuccessful();
        $this->assertStringContainsString('Classement Général 2018 - 2019', $this->client->getResponse());
    }
}
