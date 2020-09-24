<?php

namespace App\DataFixtures;

use App\Entity\Dyk;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class DykFixture extends Fixture
{
    private $dykFixture;

    public function load(ObjectManager $manager)
    {
        $this->dykFixture = new Dyk();
        $this->dykFixture->setDykText('test');

        $manager->persist($this->dykFixture);
        $manager->flush();
    }

    public function deleteFixture(ObjectManager $manager)
    {
        $manager->remove($this->dykFixture);
        $manager->flush();
    }
}
