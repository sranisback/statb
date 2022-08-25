<?php

namespace App\Controller;

use App\Entity\Citations;
use App\Entity\Coaches;
use App\Form\AjoutCitationType;
use App\Form\AjoutCoachType;
use App\Service\UtilisateurService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class UtilisateurController extends AbstractController
{

    private UtilisateurService $utilisateurService;

    public function __construct(UtilisateurService $utilisateurService)
    {
        $this->utilisateurService = $utilisateurService;
    }

    /**
     * @Route("/usercontrol", name="usercontrol")
     * @param Request $request
     * @return Response
     */
    public function interfaceUtilisateur(Request $request): Response
    {
        $citation = new Citations();

        $form = $this->createForm(AjoutCitationType::class, $citation);
        $form->handleRequest($request);

        if ($this->utilisateurService->ajoutCitation($citation, $form)) {
            $this->addFlash('success', 'Citation Ajoutée!');

            return $this->redirectToRoute('frontUser');
        }

        return $this->render(
            'statbb/tabs/parametres/addcitation.html.twig',
            ['form' => $form->createView(), 'citation' => $citation]
        );
    }

    /**
     * @Route("/creeCoach", name="creeCoach")
     * @param Request $request
     * @param UserPasswordEncoderInterface $encoder
     * @return RedirectResponse|Response
     */
    public function creeCoach(Request $request,  UserPasswordEncoderInterface $encoder): RedirectResponse|Response
    {
        $coach = new Coaches();

        $form = $this->createForm(AjoutCoachType::class, $coach);
        $form->handleRequest($request);

        if($this->utilisateurService->creeCoach($coach, $form, $encoder)) {
            $this->addFlash('success', 'Coach crée!');

            return $this->redirectToRoute('index');
        }

        return $this->render(
            'statbb/addCoach.html.twig',
            ['form' => $form->createView(), 'coach' => $coach]
        );
    }
}
