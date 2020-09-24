<?php

namespace App\DataFixtures;

use App\Entity\Coaches;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class CoachesFixture extends Fixture
{
    private $coachFixture;

    public const COACH_FIXTURE = 'coach-fixture';

    public function load(ObjectManager $manager)
    {
        $this->coachFixture = new Coaches();
        $this->coachFixture->setName('test');
        $this->coachFixture->setRoles(['role' => 'ROLE_USER']);

        $manager->persist($this->coachFixture);
        $manager->flush();

        $this->addReference(self::COACH_FIXTURE, $this->coachFixture);
    }

    public function deleteFixture(ObjectManager $manager)
    {
        $manager->remove($this->coachFixture);
        $manager->flush();
    }
}
