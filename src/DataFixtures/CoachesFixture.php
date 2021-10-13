<?php

namespace App\DataFixtures;

use App\Entity\Coaches;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class CoachesFixture extends Fixture
{
    private Coaches $coachFixture;

    public const COACH_FIXTURE = 'coach-fixture';

    public function load(ObjectManager $manager): Coaches
    {
        $this->coachFixture = new Coaches();
        $this->coachFixture->setName('test');
        $this->coachFixture->setRoles(['role' => 'ROLE_USER']);

        $manager->persist($this->coachFixture);
        $manager->flush();

        $this->addReference(self::COACH_FIXTURE, $this->coachFixture);

        return $this->coachFixture;
    }

    /**
     * @param ObjectManager $manager
     * @param int $nombre
     * @return mixed
     */
    public function loadMultiCoach(ObjectManager $manager, $nombre)
    {
        $coachFixtures = null;

        for($compteur = 0; $compteur < $nombre; $compteur++){
            $coach = new Coaches();
            $coach->setName('test_' . $compteur);
            $coach->setRoles(['role' => 'ROLE_USER']);

            $coachFixtures[] = $coach;

            $manager->persist($coach);
            $manager->flush();
        }

        return $coachFixtures;
    }

    public function deleteFixture(ObjectManager $manager) : void
    {
        $manager->remove($this->coachFixture);
        $manager->flush();
    }
}
