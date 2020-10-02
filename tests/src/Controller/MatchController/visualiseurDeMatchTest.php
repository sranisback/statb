<?php


namespace App\Tests\src\Controller\MatchController;

use App\DataFixtures\CitationFixture;
use App\DataFixtures\CoachesFixture;
use App\DataFixtures\DykFixture;
use App\DataFixtures\MatchFixture;
use App\DataFixtures\SettingFixtures;
use App\DataFixtures\TeamFixture;
use App\Entity\Matches;
use App\Tests\src\Functionnal;

class visualiseurDeMatchTest extends Functionnal
{
    /**
     * @test
     */
    public function pas_de_match()
    {
        $this->client->request('GET', '/match/25');

        $this->assertResponseIsSuccessful();
        $this->assertStringContainsString('erreur', $this->client->getResponse());
    }

    /**
     * @test
     */
    public function un_match()
    {
        $settingFixture = new SettingFixtures();
        $settingFixture->load($this->entityManager);

        $dykFixture = new DykFixture();
        $dykFixture->load($this->entityManager);

        $coachFixture = new CoachesFixture();
        $coachFixture->setReferenceRepository($this->referenceRepo);
        $coachTest = $coachFixture->load($this->entityManager);

        $citationFixture = new CitationFixture();
        $citationFixture->setReferenceRepository($this->referenceRepo);
        $citationFixture->load($this->entityManager);

        $matchFixture = new MatchFixture();
        $matchTest = $matchFixture->load($this->entityManager);

        $equipeFixture = new TeamFixture();
        $equipeFixture->setReferenceRepository($this->referenceRepo);
        $equipe0Test = $equipeFixture->load($this->entityManager);
        $equipe0Test->setName('zob');
        $equipe0Test->setOwnedByCoach($coachTest);
        $equipe1Test = $equipeFixture->load($this->entityManager);
        $equipe1Test->setName('zob');
        $equipe1Test->setOwnedByCoach($coachTest);

        $matchTest->setTeam1($equipe0Test);
        $matchTest->setTeam2($equipe1Test);

        $this->entityManager->persist($matchTest);
        $this->entityManager->flush();

        /** @var Matches $matchTest */
        $this->client->request('GET', '/match/' . $matchTest->getMatchId());

        $this->assertResponseIsSuccessful();
        $this->assertStringContainsString('Match n°', $this->client->getResponse());
    }
}
