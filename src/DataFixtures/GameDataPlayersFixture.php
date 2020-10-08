<?php

namespace App\DataFixtures;

use App\Entity\GameDataPlayers;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class GameDataPlayersFixture extends Fixture
{
    public function load(ObjectManager $manager)
    {
        $gameDataPlayerFixture = new GameDataPlayers();
        $gameDataPlayerFixture->setFRace($this->getReference(TeamFixture::TEAM_RACE_FIXTURE));
        $gameDataPlayerFixture->setQty(16);

        $manager->persist($gameDataPlayerFixture);
        $manager->flush();
    }
}
