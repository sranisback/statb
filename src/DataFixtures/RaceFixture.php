<?php


namespace App\DataFixtures;


use App\Entity\Races;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class RaceFixture extends Fixture
{
    public const RACE_FIXTURE = 'race-fixture';

    public function load(ObjectManager $manager)
    {
        $race = new Races();
        $race->setName('Test race');

        $this->addReference(self::RACE_FIXTURE, $race);

        $manager->persist($race);
        $manager->flush();

        return $race;
    }
}