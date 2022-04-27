<?php


namespace App\DataFixtures;

use App\Entity\Players;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class PlayerFixture extends Fixture
{
    /** @var Players $joueurFixture */
    private $joueurFixture;

    public function load(ObjectManager $manager) : Players
    {
        $this->joueurFixture = new Players();

        $manager->persist($this->joueurFixture);
        $manager->flush();

        return $this->joueurFixture;
    }

    public function deleteFixture(ObjectManager $manager) : void
    {
        $manager->remove($this->joueurFixture);
        $manager->flush();
    }
}
