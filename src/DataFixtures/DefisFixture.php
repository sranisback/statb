<?php

namespace App\DataFixtures;

use App\Entity\Defis;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class DefisFixture extends Fixture
{
    public function load(ObjectManager $manager)
    {
        $defisFixture = new Defis();

        $manager->persist($defisFixture);
        $manager->flush();

        return $defisFixture;
    }
}
