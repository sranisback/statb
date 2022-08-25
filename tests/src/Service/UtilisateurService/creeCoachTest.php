<?php


namespace App\Tests\src\Service\UtilisateurService;


use App\Entity\Coaches;
use App\Service\CitationService;
use App\Service\UtilisateurService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\Form\Form;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class creeCoachTest extends KernelTestCase
{
    /**
     * @test
     */
    public function le_coach_est_cree()
    {
        $coach = new Coaches();
        $coach->setUsername("Test");
        $coach->setPassword("testMdp");

        $objectManager = $this->createMock(EntityManagerInterface::class);
        $objectManager->expects($this->once())->method('persist')->with($coach);
        $objectManager->expects($this->once())->method('flush');

        $form = $this->createMock(Form::class);
        $form->method('isSubmitted')->willReturn(true);
        $form->method('isValid')->willReturn(true);

        $encoder = $this->createMock(UserPasswordEncoderInterface::class);
        $encoder->method('encodePassword')->willReturn('$2y$13$.9RH8XF1FYqF6BvYIBD5B.tm8VZjeN8gL0jKaX8.qU.76MUVuYPzK');

        $utilisateurService = new UtilisateurService(
            $objectManager,
            $this->createMock(CitationService::class)
        );

        $utilisateurService->creeCoach($coach, $form, $encoder);
    }
}