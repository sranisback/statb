<?php

namespace App\DataFixtures;

use App\Entity\GameDataPlayers;
use App\Entity\Races;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class GameDataPlayersFixture extends Fixture
{
    public function load(ObjectManager $manager) : void
    {
        $gameDataPlayerFixture = new GameDataPlayers();
        /** @var Races $teamRace */
        $teamRace = $this->getReference(TeamFixture::TEAM_RACE_FIXTURE);
        $gameDataPlayerFixture->setFRace($teamRace);
        $gameDataPlayerFixture->setQty(16);

        $manager->persist($gameDataPlayerFixture);
        $manager->flush();
    }
}
