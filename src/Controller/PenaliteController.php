<?php


namespace App\Controller;

use App\Entity\Penalite;
use App\Form\AjoutPenaliteForm;
use App\Service\PenaliteService;
use App\Service\SettingsService;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class PenaliteController extends AbstractController
{

    private ManagerRegistry $doctrine;

    public function __construct(ManagerRegistry $doctrine)
    {
        $this->doctrine = $doctrine;
    }
    /**
     * @Route("/ajoutPenaliteForm", name="ajoutPenaliteForm")
     * @param Request $request
     * @param PenaliteService $penaliteService
     * @return Response
     */
    public function ajoutPenaliteForm(
        Request $request,
        PenaliteService $penaliteService
    ): Response {
        $penalite = new Penalite();

        $form = $this->createForm(AjoutPenaliteForm::class, $penalite);
        $form->handleRequest($request);

        if($penaliteService->creerUnePenalite($penalite)) {
            $this->addFlash('success', 'Penalité Ajouté!');

            return $this->redirectToRoute('frontUser');
        }

        return $this->render('statbb/addPenalite.html.twig', ['form' => $form->createView(), 'penalite' => $penalite]);
    }

    /**
     * @Route("/afficherPenalite", name="afficherPenalite")
     * @param SettingsService $settingsService
     * @return Response
     */
    public function afficherPenalite(SettingsService $settingsService): Response
    {
        return $this->render(
            'statbb/tabs/ligue/affichagePenalite.html.twig',
            [
                'penaliteCollection' => $this->doctrine->getRepository(Penalite::class)->listePenaliteEnCours(
                    $settingsService->anneeCourante()
                ),
            ]
        );
    }
}
