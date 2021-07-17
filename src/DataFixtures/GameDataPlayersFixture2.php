<?php

namespace App\DataFixtures;

use App\Entity\GameDataPlayers;
use App\Entity\Races;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class GameDataPlayersFixture2 extends Fixture
{
    public function load(ObjectManager $manager)
    {
        $race = new Races();
        $race->setName('Test');

        $manager->persist($race);

        $gameDataPlayerFixture = new GameDataPlayers();
        $gameDataPlayerFixture->setFRace($race);
        $gameDataPlayerFixture->setQty(16);

        $manager->persist($gameDataPlayerFixture);
        $manager->flush();

        return $gameDataPlayerFixture;
    }
}
