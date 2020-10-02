<?php


namespace App\Tests\src\Controller\MatchController;


use App\DataFixtures\CoachesFixture;
use App\Tests\src\Functionnal;

class matchsDunCoachTest extends Functionnal
{
    /**
     * @test
     */
    public function match_dun_coach()
    {
        $coachFixture = new CoachesFixture();
        $coachFixture->setReferenceRepository($this->referenceRepo);
        $coach = $coachFixture->load($this->entityManager);

        $this->client->loginUser($coach);

        $this->client->request('GET', '/anciensMatchs');

        $this->assertResponseIsSuccessful();
        $this->assertStringContainsString('listeAnciensMatchs', $this->client->getResponse());
    }
}