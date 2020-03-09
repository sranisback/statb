<?php

namespace App\Controller;

use App\Entity\Citations;
use App\Form\AjoutCitationType;
use App\Service\CitationService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class UtilisateurController extends AbstractController
{
    /**
     * @Route("/usercontrol/", name="usercontrol", options ={"expose"= true})
     * @return Response
     */
    public function interfaceUtilisateur(): \Symfony\Component\HttpFoundation\Response
    {
        $citation = new Citations();

        $form = $this->createForm(AjoutCitationType::class, $citation);

        return $this->render('statbb/tabs/parametres/addcitation.html.twig', ['form' => $form->createView()]);
    }

    /**
     * @Route("/ajoutCitation", name="ajoutCitation", options = { "expose" = true })
     * @param Request $request
     * @param CitationService $citationService
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function interfaceUtilisateurRetour(Request $request, CitationService $citationService): \Symfony\Component\HttpFoundation\RedirectResponse
    {
        $citationService->enregistrerCitation($request->request->get('ajout_citation'));

        $this->addFlash('success', 'Citation Ajoutée!');

        return $this->redirectToRoute('frontUser');
    }
}
