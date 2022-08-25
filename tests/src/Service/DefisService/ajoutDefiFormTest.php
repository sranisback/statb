<?php


namespace App\Tests\src\Service\DefisService;


use App\Entity\Coaches;
use App\Entity\Defis;
use App\Entity\GameDataSkills;
use App\Entity\PlayersSkills;
use App\Entity\Teams;
use App\Service\DefisService;
use App\Service\InfosService;
use App\Service\SettingsService;
use Cassandra\Set;
use Doctrine\ORM\EntityManagerInterface;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Form\Form;

class ajoutDefiFormTest extends TestCase
{

    private $form;

    private Defis $defis;

    private DefisService $defiService;

    private $settingService;

    public function setUp(): void
    {
        parent::setUp();

        $coach = new Coaches();

        $team = new Teams();
        $team->setOwnedByCoach($coach);

        $this->defis = new Defis();
        $this->defis->setEquipeOrigine($team);

        $this->form = $this->createMock(Form::class);

        $teamRepo = $this->getMockBuilder(Teams::class)
            ->addMethods(['findBy'])
            ->getMock();
        $teamRepo->method('findBy')->willReturn([$team]);

        $defiRepo = $this->getMockBuilder(Defis::class)
            ->addMethods(['findBy'])
            ->getMock();
        $defiRepo->method('findBy')->willReturn([$this->defis]);

        $objectManager = $this->createMock(EntityManagerInterface::class);
        $objectManager->method('getRepository')->will(
            $this->returnCallback(
                function ($entityName) use ($teamRepo, $defiRepo) {
                    if ($entityName === Teams::class) {
                        return $teamRepo;
                    }

                    if ($entityName === Defis::class) {
                        return $defiRepo;
                    }

                    return true;
                }
            )
        );

        $this->settingService = $this->createMock(SettingsService::class);
        $this->settingService->method('anneeCourante')->willReturn(1);

        $this->defiService = new DefisService(
            $objectManager,
            $this->createMock(InfosService::class),
            $this->settingService
        );
    }

    /**
     * @test
     */
    public function le_defi_est_ok()
    {
        $this->form->method('isSubmitted')->willReturn(true);
        $this->form->method('isValid')->willReturn(true);

        $this->settingService->method('dateDansLaPeriodeCourante')->willReturn(false);

        $this->assertEquals(1,$this->defiService->ajoutDefiForm($this->form, $this->defis));
    }

    /**
     * @test
     */
    public function plus_de_defi_dans_la_periode()
    {
        $this->form->method('isSubmitted')->willReturn(true);
        $this->form->method('isValid')->willReturn(true);

        $this->settingService->method('dateDansLaPeriodeCourante')->willReturn(true);

        $this->assertEquals(2,$this->defiService->ajoutDefiForm($this->form, $this->defis));
    }

    /**
     * @test
     */
    public function pas_de_defi_du_tout()
    {
        $this->form->method('isSubmitted')->willReturn(false);
        $this->form->method('isValid')->willReturn(false);

        $this->assertEquals(3,$this->defiService->ajoutDefiForm($this->form, $this->defis));
    }
}