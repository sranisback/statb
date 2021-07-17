<?php


namespace App\DataFixtures;


use App\Entity\GameDataSkills;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class GameDataSkillFixture extends Fixture
{
    public function load(ObjectManager $manager)
    {
        $gameDataSkillFixture = new GameDataSkills();
        $gameDataSkillFixture->setName('Comp test');
        $gameDataSkillFixture->setCat('N');

        $manager->persist($gameDataSkillFixture);
        $manager->flush();

        return $gameDataSkillFixture;
    }
}