<?php

namespace App\DataFixtures;

use App\Entity\GameDataStadium;
use App\Entity\Stades;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class StadesFixture extends Fixture
{
    public function load(ObjectManager $manager)
    {
        $gameDataStadium = new GameDataStadium();
        $gameDataStadium->setFamille('zib');
        $gameDataStadium->setType('zib²');
        $gameDataStadium->setEffect('hihi');

        $stadeFixture = new Stades();
        $stadeFixture->setFTypeStade($gameDataStadium);

        $manager->persist($gameDataStadium);
        $manager->persist($stadeFixture);
        $manager->flush();

        return $stadeFixture;
    }
}
