<?php

namespace App\DataFixtures;

use App\Entity\Dyk;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class DykFixture extends Fixture
{
    private Dyk $dykFixture;

    public function load(ObjectManager $manager): Dyk
    {
        $this->dykFixture = new Dyk();
        $this->dykFixture->setDykText('test');

        $manager->persist($this->dykFixture);
        $manager->flush();

        return $this->dykFixture;
    }

    public function deleteFixture(ObjectManager $manager) : void
    {
        $manager->remove($this->dykFixture);
        $manager->flush();
    }
}
