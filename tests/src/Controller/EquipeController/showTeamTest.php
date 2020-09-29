<?php


namespace App\Tests\src\Controller\EquipeController;


use App\DataFixtures\CitationFixture;
use App\DataFixtures\CoachesFixture;
use App\DataFixtures\DykFixture;
use App\DataFixtures\SettingFixtures;
use App\DataFixtures\TeamFixture;
use App\Tests\src\Functionnal;

class showTeamTest extends Functionnal
{
    /**
     * @test
     */
    public function une_equipe_est_montre()
    {
        $settingFixture = new SettingFixtures();
        $settingFixture->load($this->entityManager);

        $dykFixture = new DykFixture();
        $dykFixture->load($this->entityManager);

        $coachFixture = new CoachesFixture();
        $coachFixture->setReferenceRepository($this->referenceRepo);
        $coach = $coachFixture->load($this->entityManager);
        $this->client->loginUser($coach);

        $citationFixture = new CitationFixture();
        $citationFixture->setReferenceRepository($this->referenceRepo);
        $citationFixture->load($this->entityManager);

        $teamFixture = new TeamFixture();
        $teamFixture->setReferenceRepository($this->referenceRepo);

        $equipe = $teamFixture->load($this->entityManager);
        $equipe->setName('test');
        $equipe->setOwnedByCoach($coach);

        $this->entityManager->persist($equipe);
        $this->entityManager->flush();

        $this->client->request('GET', '/team/' . $equipe->getTeamId());

        $this->assertResponseIsSuccessful();
        $this->assertStringContainsString('<strong>test</strong>', $this->client->getResponse());
    }
}