<?php

namespace App\Controller\Admin;

use App\Entity\GameDataStadium;
use App\Entity\Stades;
use App\Entity\Teams;
use App\Enum\AnneeEnum;
use App\Form\admin\TeamsType;
use App\Repository\TeamRepository;
use App\Service\SettingsService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/Admin/teams")
 */
class TeamsController extends AbstractController
{
    /**
     * @Route("/", name="teams_index", methods={"GET"})
     */
    public function index(TeamRepository $teamRepository): Response
    {
        return $this->render('statbb/admin/teams/index.html.twig', [
            'teams' => $teamRepository->findAll(),
            'etiquetteAnne' => (new AnneeEnum)->numeroToAnnee()
        ]);
    }

    /**
     * @Route("/new", name="teams_new", methods={"GET","POST"})
     */
    public function new(Request $request, SettingsService $settingsService): Response
    {
        $team = new Teams();
        $form = $this->createForm(TeamsType::class, $team);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();

            $stade = new Stades();
            $typeStade = $entityManager->getRepository(GameDataStadium::class)->findOneBy(['id' => 0]);

            $stade->setFTypeStade($typeStade);
            $stade->setTotalPayement(0);
            $stade->setNom('La prairie verte');
            $stade->setNiveau(0);
            $entityManager->persist($stade);

            $team->setTreasury($settingsService->recupererTresorDepart());
            $team->setYear($settingsService->anneeCourante());
            $team->setFStades($stade);

            $entityManager->persist($team);
            $entityManager->flush();

            return $this->redirectToRoute('teams_index');
        }

        return $this->render('statbb/admin/teams/new.html.twig', [
            'team' => $team,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{teamId}/edit", name="teams_edit", methods={"GET","POST"})
     */
    public function edit(Request $request, Teams $team): Response
    {
        $form = $this->createForm(TeamsType::class, $team);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('teams_index');
        }

        return $this->render('statbb/admin/teams/edit.html.twig', [
            'team' => $team,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{teamId}", name="teams_delete", methods={"DELETE"})
     */
    public function delete(Request $request, Teams $team): Response
    {
        if ($this->isCsrfTokenValid('delete'.$team->getTeamId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($team);
            $entityManager->flush();
        }

        return $this->redirectToRoute('teams_index');
    }
}
