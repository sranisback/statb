<?php


namespace App\Tests\src\Service\PenaliteService;


use App\Entity\Penalite;
use App\Service\PenaliteService;
use Doctrine\ORM\EntityManagerInterface;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Form\Form;

class creerUnePenaliteTest extends TestCase
{
    private $form;

    private $objectManager;

    private PenaliteService $penaliteService;

    public function setUp(): void
    {
        parent::setUp();

        $this->form = $this->createMock(Form::class);

        $this->objectManager = $this->createMock(EntityManagerInterface::class);

        $this->penaliteService = new PenaliteService(
            $this->objectManager
        );
    }


    /**
     * @test
     */
    public function une_penalite_est_cree()
    {
        $penalite = new Penalite();

        $this->form->method('isSubmitted')->willReturn(true);
        $this->form->method('isValid')->willReturn(true);

        $this->objectManager->expects($this->once())->method('persist')->with($penalite);
        $this->objectManager->expects($this->once())->method('flush');

        $this->assertTrue($this->penaliteService->creerUnePenalite($penalite, $this->form));
    }

    /**
     * @test
     */
    public function le_form_est_pas_valide()
    {
        $penalite = new Penalite();

        $this->form->method('isSubmitted')->willReturn(false);
        $this->form->method('isValid')->willReturn(false);

        $this->penaliteService->creerUnePenalite($penalite, $this->form);

        $this->assertFalse($this->penaliteService->creerUnePenalite($penalite, $this->form));
    }
}