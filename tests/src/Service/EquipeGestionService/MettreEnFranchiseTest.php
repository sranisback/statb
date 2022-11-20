<?php


namespace App\Tests\src\Service\EquipeGestionService;


use App\Entity\Teams;
use App\Service\EquipeGestionService;
use App\Service\InducementService;
use App\Service\InfosService;
use App\Service\SettingsService;
use App\Tests\src\TestServiceFactory\EquipeGestionServiceTestFactory;
use Doctrine\ORM\EntityManager;
use PHPUnit\Framework\TestCase;

class MettreEnFranchiseTest extends TestCase
{
    /**
     * @test
     */
    public function la_franchise_est_activee()
    {
        $equipeTest = new Teams();
        $equipeTest->setFranchise(false);

        $objectManager = $this->createMock(EntityManager::class);

        $equipeGestionServiceTest = (new EquipeGestionServiceTestFactory())->getInstance(
            $objectManager
        );

        $equipeExpected = new Teams();
        $equipeExpected->setFranchise(true);

        $objectManager->expects($this->exactly(1))->method('refresh')->with($equipeExpected);

        $equipeGestionServiceTest->mettreEnFranchise($equipeTest);
    }
    /**
     * @test
     */
    public function la_franchise_est_desactivee()
    {
        $equipeTest = new Teams();
        $equipeTest->setFranchise(true);

        $objectManager = $this->createMock(EntityManager::class);

        $equipeGestionServiceTest = (new EquipeGestionServiceTestFactory())->getInstance(
            $objectManager
        );

        $equipeExpected = new Teams();
        $equipeExpected->setFranchise(false);

        $objectManager->expects($this->exactly(1))->method('refresh')->with($equipeExpected);

        $equipeGestionServiceTest->mettreEnFranchise($equipeTest);
    }


}