<?php


namespace App\DataFixtures;


use App\Entity\GameDataSkills;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class GameDataSkillFanFavoriteFixture extends Fixture
{
    public function load(ObjectManager $manager) : void
    {
        $gameDataSkillsFanFavorite = new GameDataSkills();
        $gameDataSkillsFanFavorite->setName('Fan Favorite');

        $manager->persist($gameDataSkillsFanFavorite);
        $manager->flush();
    }
}