<?php

namespace App\Controller;

use App\Entity\Citations;
use App\Entity\Coaches;
use App\Form\AjoutCitationType;
use App\Form\AjoutCoachType;
use App\Service\CitationService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class UtilisateurController extends AbstractController
{
    /**
     * @Route("/usercontrol", name="usercontrol")
     * @param Request $request
     * @param CitationService $citationService
     * @return Response
     */
    public function interfaceUtilisateur(Request $request, CitationService $citationService): Response
    {
        $citation = new Citations();

        $form = $this->createForm(AjoutCitationType::class, $citation);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            /** @var array $citation */
            $citation = $request->request->get('ajout_citation');
            $citationService->enregistrerCitation($citation);

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
     * @return RedirectResponse|Response
     */
    public function creeCoach(Request $request,  UserPasswordEncoderInterface $encoder)
    {
        $coach = new Coaches();

        $form = $this->createForm(AjoutCoachType::class, $coach);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $coach->setRoles(['role' => 'ROLE_USER']);

            $encoded = $encoder->encodePassword($coach, $coach->getPassword());
            $coach->setPassword($encoded);

            $entityManager = $this->getDoctrine()->getManager();

            $entityManager->persist($coach);
            $entityManager->flush();

            $this->addFlash('success', 'Coach crée!');

            return $this->redirectToRoute('index');
        }

        return $this->render(
            'statbb/addCoach.html.twig',
            ['form' => $form->createView(), 'coach' => $coach]
        );

    }
}
