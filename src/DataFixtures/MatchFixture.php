<?php

namespace App\DataFixtures;

use App\Entity\GameDataStadium;
use App\Entity\Matches;
use App\Entity\Meteo;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class MatchFixture extends Fixture
{
    public function load(ObjectManager $manager) : Matches
    {
        $meteo = new Meteo();
        $meteo->setNom('zob');

        $gameDataStadium = new GameDataStadium();
        $gameDataStadium->setFamille('zob');
        $gameDataStadium->setType('zob');
        $gameDataStadium->setEffect('zob');

        $matchFixture = new Matches();
        $matchFixture->setFMeteo($meteo);
        $matchFixture->setFStade($gameDataStadium);

        $manager->persist($gameDataStadium);
        $manager->persist($meteo);
        $manager->persist($matchFixture);
        $manager->flush();

        return $matchFixture;
    }
}
