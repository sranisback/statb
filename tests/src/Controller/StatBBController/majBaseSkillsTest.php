<?php


namespace App\Tests\src\Controller\StatBBController;


use App\DataFixtures\GameDataPlayersFixture2;
use App\DataFixtures\GameDataSkillFixture;
use App\Entity\GameDataPlayers;
use App\Tests\src\Functionnal;

class majBaseSkillsTest extends Functionnal
{
    /**
     * @test
     */
    public function les_comps_sont_biens_maj()
    {
        $listSkill = '';

        $gameDataPlayersFixture = new GameDataPlayersFixture2();
        $gameDataPlayersTest = $gameDataPlayersFixture->load($this->entityManager);

        $gameDataSkillFixture = new GameDataSkillFixture();
        $gameDataSkillTest = $gameDataSkillFixture->load($this->entityManager);

        $listSkill .= $gameDataSkillTest->getSkillId() . ', ';

        $gameDataSkillFixture = new GameDataSkillFixture();
        $gameDataSkillTest =$gameDataSkillFixture->load($this->entityManager);

        $listSkill .= $gameDataSkillTest->getSkillId() . ', ';

        $gameDataSkillFixture = new GameDataSkillFixture();
        $gameDataSkillTest = $gameDataSkillFixture->load($this->entityManager);

        $listSkill .= $gameDataSkillTest->getSkillId();

        $gameDataPlayersTest->setSkills($listSkill);

        $this->entityManager->persist($gameDataPlayersTest);
        $this->entityManager->flush();

        $this->client->request('GET', '/majBaseSkill');

        $retour = $this->entityManager->getRepository(GameDataPlayers::class)->findOneBy(['posId' => $gameDataPlayersTest->getPosId()]);

        $this->assertNotNull($retour->getSkills());

        $listeCompTest = $retour->getBaseSkills();

        $this->assertEquals(3,count($listeCompTest));
    }

    /**
     * @test
     */
    public function la_position_n_a_pas_de_comp_de_base()
    {
        $gameDataPlayersFixture = new GameDataPlayersFixture2();
        $gameDataPlayersTest = $gameDataPlayersFixture->load($this->entityManager);

        $this->client->request('GET', '/majBaseSkill');

        $retour = $this->entityManager->getRepository(GameDataPlayers::class)->findOneBy(['posId' => $gameDataPlayersTest->getPosId()]);

        $this->assertNull($retour->getSkills());

        $listeCompTest = $retour->getBaseSkills();

        $this->assertEquals(0,count($listeCompTest));
    }
}