<?php

namespace App\DataFixtures;

use App\Entity\PlayersIcons;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class PlayersIconsFixture extends Fixture
{
    public function load(ObjectManager $manager) : void
    {
        $playerIconsFixture = new PlayersIcons();
        $playerIconsFixture->setIconName('nope');

        $manager->persist($playerIconsFixture);
        $manager->flush();
    }
}
