<?php


namespace App\DataFixtures;


use App\Entity\Players;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class PlayerFixture extends Fixture
{
    private $joueurFixture;

    public function load(ObjectManager $manager)
    {
        $this->joueurFixture = new Players();

        $manager->persist($this->joueurFixture);
        $manager->flush();

        return $this->joueurFixture;
    }

    public function deleteFixture(ObjectManager $manager)
    {
        $manager->remove($this->joueurFixture);
        $manager->flush();
    }
}