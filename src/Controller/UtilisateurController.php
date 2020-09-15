<?php

namespace App\Controller;

use App\Entity\Citations;
use App\Form\AjoutCitationType;
use App\Service\CitationService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\Encoder\XmlEncoder;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use Symfony\Component\Serializer\Serializer;

class UtilisateurController extends AbstractController
{
    /**
     * @param mixed $response
     * @return JsonResponse
     */
    public static function transformeEnJson($response): JsonResponse
    {
        $encoders = array(new XmlEncoder(), new JsonEncoder());
        $normalizers = array(new ObjectNormalizer());

        $serializer = new Serializer($normalizers, $encoders);

        $jsonContent = $serializer->serialize($response, 'json');

        return new JsonResponse($jsonContent);
    }

    /**
     * @Route("/usercontrol/", name="usercontrol")
     * @return Response
     */
    public function interfaceUtilisateur(): \Symfony\Component\HttpFoundation\Response
    {
        $citation = new Citations();

        $form = $this->createForm(AjoutCitationType::class, $citation);

        return $this->render('statbb/tabs/parametres/addcitation.html.twig', ['form' => $form->createView()]);
    }

    /**
     * @Route("/ajoutCitation", name="ajoutCitation")
     * @param Request $request
     * @param CitationService $citationService
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function interfaceUtilisateurRetour(
        Request $request,
        CitationService $citationService
    ): \Symfony\Component\HttpFoundation\RedirectResponse {
        $citationService->enregistrerCitation($request->request->get('ajout_citation'));

        $this->addFlash('success', 'Citation Ajoutée!');

        return $this->redirectToRoute('frontUser');
    }
}
