<?php


namespace App\Tests\src\Service\UtilisateurService;


use App\Entity\Citations;
use App\Entity\Coaches;
use App\Service\CitationService;
use App\Service\UtilisateurService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\Form\Form;

class interfaceUtilisateurTest extends KernelTestCase
{
    /**
     * @test
     */
    public function la_citation_est_ajoutee()
    {
        $form = $this->createMock(Form::class);
        $form->method('isSubmitted')->willReturn(true);
        $form->method('isValid')->willReturn(true);

        $utilisateurService = new UtilisateurService(
            $this->createMock(EntityManagerInterface::class),
            $this->createMock(CitationService::class)
        );

        $this->assertTrue($utilisateurService->ajoutCitation(new Citations(), $form));
    }
}