<?php


namespace App\Tests\src\Service\PlayerService;


use App\Entity\GameDataPlayers;
use App\Entity\GameDataSkills;
use App\Entity\Players;
use App\Tests\src\TestServiceFactory\PlayerServiceTestFactory;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\EntityManagerInterface;
use PHPUnit\Framework\TestCase;

class LeJoueurEstDisposableTest extends TestCase
{
    /**
     * @test
     */
    public function le_joueur_est_disposable()
    {
        $gameDataSkillsTest = $this->createMock(GameDataSkills::class);
        $gameDataSkillsTest->method('getName')->willReturn('Disposable');
        $gameDataSkillsTest->method('getSkillId')->willReturn(1);

        $testbaseSkills = new ArrayCollection([$gameDataSkillsTest]);

        $positionmock = $this->createMock(GameDataPlayers::class);
        $positionmock->method('getBaseSkills')->willReturn(
            $testbaseSkills
        );

        $joueurMock = $this->createMock(Players::class);
        $joueurMock->method('getFPos')->willReturn($positionmock);

        $gameDataSkillsRepoMock = $this->getMockBuilder(Players::class)
            ->addMethods(['findOneBy'])
            ->getMock();
        $gameDataSkillsRepoMock->method('findOneBy')->willReturn($gameDataSkillsTest);

        $objectManager = $this->createMock(EntityManagerInterface::class);
        $objectManager->method('getRepository')->willReturn($gameDataSkillsRepoMock);

        $playerServiceTest = (new PlayerServiceTestFactory)->getInstance(
            $objectManager
        );

        $this->assertTrue($playerServiceTest->leJoueurEstDisposable($joueurMock));
    }

    /**
     * @test
     */
    public function le_joueur_est_pas_disposable()
    {
        $gameDataSkillsTest = $this->createMock(GameDataSkills::class);
        $gameDataSkillsTest->method('getName')->willReturn('Disposable');
        $gameDataSkillsTest->method('getSkillId')->willReturn(1);

        $testbaseSkills = new ArrayCollection();

        $positionmock = $this->createMock(GameDataPlayers::class);
        $positionmock->method('getBaseSkills')->willReturn(
            $testbaseSkills
        );

        $joueurMock = $this->createMock(Players::class);
        $joueurMock->method('getFPos')->willReturn($positionmock);

        $gameDataSkillsRepoMock = $this->getMockBuilder(Players::class)
            ->addMethods(['findOneBy'])
            ->getMock();
        $gameDataSkillsRepoMock->method('findOneBy')->willReturn($gameDataSkillsTest);

        $objectManager = $this->createMock(EntityManagerInterface::class);
        $objectManager->method('getRepository')->willReturn($gameDataSkillsRepoMock);

        $playerServiceTest = (new PlayerServiceTestFactory)->getInstance(
            $objectManager
        );

        $this->assertFalse($playerServiceTest->leJoueurEstDisposable($joueurMock));
    }

}