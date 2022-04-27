<?php

namespace App\DataFixtures;

use App\Entity\Races;
use App\Entity\Teams;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class TeamFixture extends Fixture
{
    /** @var Teams $equipeFixture */
    private $equipeFixture;

    public const TEAM_RACE_FIXTURE = 'team-race-fixture';

    public function load(ObjectManager $manager) : Teams
    {
        $this->equipeFixture = new Teams();

        $race = new Races();
        $race->setName('Test race');

        $this->equipeFixture->setFRace($race);

        $this->setReference(self::TEAM_RACE_FIXTURE, $race);

        $manager->persist($race);
        $manager->persist($this->equipeFixture);
        $manager->flush();

        return $this->equipeFixture;
    }

    public function deleteFixture(ObjectManager $manager) : void
    {
        $manager->remove($this->equipeFixture);
        $manager->flush();
    }
}
