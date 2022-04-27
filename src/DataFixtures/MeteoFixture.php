<?php

namespace App\DataFixtures;

use App\Entity\Meteo;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class MeteoFixture extends Fixture
{
    public function load(ObjectManager $manager) : Meteo
    {
        $meteoFixture = new Meteo();
        $meteoFixture->setNom('zob');

        $manager->persist($meteoFixture);
        $manager->flush();

        return $meteoFixture;
    }
}
