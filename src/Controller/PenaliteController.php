<?php


namespace App\Controller;

use App\Entity\Penalite;
use App\Entity\Teams;
use App\Form\AjoutPenaliteForm;
use App\Service\PenaliteService;
use App\Service\SettingsService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class PenaliteController extends AbstractController
{
    /**
     * @Route("/ajoutPenaliteForm", name="ajoutPenaliteForm")
     * @param Request $request
     * @param PenaliteService $penaliteService
     * @return Response
     */
    public function ajoutPenaliteForm(
        Request $request,
        PenaliteService $penaliteService
    ): Response
    {
        $penalite = new Penalite();

        $form = $this->createForm(AjoutPenaliteForm::class, $penalite);
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()) {
            /** @var array $datas */
            $datas = $request->request->get('ajout_penalite_form');

            /** @var Teams $equipe */
            $equipe = $this->getDoctrine()->getRepository(Teams::class)->findOneBy(['teamId' => $datas['equipe']]);

            if (!empty($equipe)) {
                $penaliteService->creerUnePenalite($datas);
                $this->addFlash('success', 'Penalité Ajouté!');
            }

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
                'penaliteCollection' => $this->getDoctrine()->getRepository(Penalite::class)->listePenaliteEnCours(
                    $settingsService->anneeCourante()
                ),
            ]
        );
    }
}
