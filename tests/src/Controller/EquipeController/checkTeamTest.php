<?php


namespace App\Tests\src\Controller\EquipeController;


use App\DataFixtures\GameDataPlayersFixture;
use App\DataFixtures\GameDataSkillFanFavoriteFixture;
use App\DataFixtures\PlayersIconsFixture;
use App\DataFixtures\TeamFixture;
use App\Tests\src\Functionnal;

class checkTeamTest extends Functionnal
{
    /**
     * @test
     */
    public function check_test()
    {
        $equipeFixture = new TeamFixture();
        $equipeFixture->setReferenceRepository($this->referenceRepo);
        $equipeTest = $equipeFixture->load($this->entityManager);

        $gameDataPlayerFixture = new GameDataPlayersFixture();
        $gameDataPlayerFixture->setReferenceRepository($this->referenceRepo);
        $gameDataPlayerFixture->load($this->entityManager);

        $playerIconFixture = new PlayersIconsFixture();
        $playerIconFixture->load($this->entityManager);

        $gameDataPlayerFixture = new GameDataSkillFanFavoriteFixture();
        $gameDataPlayerFixture->load($this->entityManager);

        $this->client->request('GET', '/checkteam/' . $equipeTest->getTeamId() );

        $this->assertResponseStatusCodeSame(302);
    }

    /**
     * @test
     */
    public function check_ne_trouve_pas_l_equipe()
    {
        $this->client->request('GET', '/checkteam/1' );

        $this->assertResponseStatusCodeSame(302);
    }
}