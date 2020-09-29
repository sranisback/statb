<?php


namespace App\Tests\src\Controller\ClassementController;

use App\DataFixtures\CitationFixture;
use App\DataFixtures\CoachesFixture;
use App\DataFixtures\DykFixture;
use App\Tests\src\Functionnal;

class matchesContreCoachTest extends Functionnal
{
    /**
     * @test
     */
    public function liste_match_contre_coach()
    {
        $dykFixture = new DykFixture();
        $dykFixture->load($this->entityManager);

        $coachesFixture = new CoachesFixture();
        $coachesFixture->setReferenceRepository($this->referenceRepo);
        $coachesFixture->load($this->entityManager);

        $citationFixture = new CitationFixture();
        $citationFixture->setReferenceRepository($this->referenceRepo);
        $citationFixture->load($this->entityManager);

        $coachMultiFixture = new CoachesFixture();
        $coachMultiFixture->setReferenceRepository($this->referenceRepo);
        $coaches = $coachMultiFixture->loadMultiCoach($this->entityManager, 2);

        $this->client->loginUser($coaches[0]);

        $this->client->request('GET', '/matchesContreCoach/' . $coaches[1]->getCoachId());

        $this->assertResponseIsSuccessful();
        $this->assertStringContainsString('Matches contre', $this->client->getResponse());
    }
}
