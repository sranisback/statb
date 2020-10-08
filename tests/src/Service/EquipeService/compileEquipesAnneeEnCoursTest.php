<?php


namespace App\Tests\src\Service\EquipeService;

use App\Entity\ClassementGeneral;
use App\Entity\Coaches;
use App\Entity\Matches;
use App\Entity\Teams;
use App\Service\EquipeService;
use App\Service\SettingsService;
use App\Tests\src\Functionnal;
use Doctrine\ORM\EntityManager;

class compileEquipesAnneeEnCoursTest extends Functionnal
{
    /**
     * @test
     */
    public function une_compilation_d_equipe_est_retournee()
    {
        $settingServiceMock = $this->createMock(SettingsService::class);
        $settingServiceMock->method('anneeCourante')->willReturn(2);

        $coachMock = $this->createMock(Coaches::class);

        $equipeMock0 = $this->createMock(Teams::class);

        $teamRepoMock = $this->getMockBuilder(Teams::class)
            ->addMethods(['toutesLesEquipesDunCoachParAnnee'])->getMock();
        $teamRepoMock->method('toutesLesEquipesDunCoachParAnnee')->willReturn(
            [$equipeMock0]
        );

        $matchMock0 = $this->createMock(Matches::class);

        $matchRepoMock = $this->getMockBuilder(Matches::class)
            ->addMethods(['listeDesMatchs'])->getMock();
        $matchRepoMock->method('listeDesMatchs')->willReturn(
            [$matchMock0]
        );

        $classementGeneralRepoMock = $this->getMockBuilder(ClassementGeneral::class)
            ->addMethods(['findOneBy'])->getMock();
        $classementGeneralRepoMock->method('findOneBy')
            ->willReturn(null);

        $objectManager = $this->createMock(EntityManager::class);
        $objectManager->method('getRepository')->will(
            $this->returnCallback(
                function ($entityName) use ($teamRepoMock,$matchRepoMock,$classementGeneralRepoMock) {
                    if ($entityName === Teams::class) {
                        return $teamRepoMock;
                    }

                    if ($entityName === Matches::class) {
                        return $matchRepoMock;
                    }

                    if ($entityName === ClassementGeneral::class) {
                        return $classementGeneralRepoMock;
                    }
                    return true;
                }
            )
        );

        $equipeServiceTest = new EquipeService(
            $objectManager,
            $settingServiceMock
        );

        $resultats = [
            'bonus' => 0,
            'tdMis' => 0,
            'tdPris' => 0,
            'sortiesPour' => 0,
            'sortiesContre' => 0,
            'score' => 0,
            'penalite' => 0,
            'win' => 0,
            'draw' => 0,
            'loss' => 0
        ];

        $attendu = [
            [
                'equipe' => $equipeMock0,
                'resultats' => $resultats
            ]
        ];

        $this->assertEquals(
            $attendu,
            $equipeServiceTest->compileEquipesAnneeEnCours($coachMock)
        );
    }
}
