<?php

namespace App\Controller\Admin;

use App\Entity\HistoriqueBlessure;
use App\Enum\BlessuresEnum;
use App\Form\Admin\HistoriqueBlessureType;
use App\Repository\HistoriqueBlessureRepository;
use App\Service\AdminService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("Admin/historique/blessure")
 */
class HistoriqueBlessureController extends AbstractController
{
    /**
     * @Route("/", name="historique_blessure_index", methods={"GET"})
     */
    public function index(HistoriqueBlessureRepository $historiqueBlessureRepository): Response
    {
        return $this->render('statbb/admin/historique_blessure/index.html.twig', [
            'historique_blessures' => $historiqueBlessureRepository->findAll(),
            'etiquette' => BlessuresEnum::numeroToBlessure()
        ]);
    }

    /**
     * @Route("/new", name="historique_blessure_new", methods={"GET","POST"})
     */
    public function new(Request $request): Response
    {
        $historiqueBlessure = new HistoriqueBlessure();
        $form = $this->createForm(HistoriqueBlessureType::class, $historiqueBlessure);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($historiqueBlessure);
            $entityManager->flush();

            return $this->redirectToRoute('historique_blessure_index');
        }

        return $this->render('statbb/admin/historique_blessure/new.html.twig', [
            'historique_blessure' => $historiqueBlessure,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}/edit", name="historique_blessure_edit", methods={"GET","POST"})
     */
    public function edit(Request $request, HistoriqueBlessure $historiqueBlessure): Response
    {
        $form = $this->createForm(HistoriqueBlessureType::class, $historiqueBlessure);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('historique_blessure_index');
        }

        return $this->render('statbb/admin/historique_blessure/edit.html.twig', [
            'historique_blessure' => $historiqueBlessure,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="historique_blessure_delete", methods={"DELETE"})
     */
    public function delete(Request $request, HistoriqueBlessure $historiqueBlessure): Response
    {
        if ($this->isCsrfTokenValid('delete'.$historiqueBlessure->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($historiqueBlessure);
            $entityManager->flush();
        }

        return $this->redirectToRoute('historique_blessure_index');
    }

    /**
     * @param Request $request
     * @Route("/updateEditableHisto", name="updateEditableHisto", options = { "expose" = true })
     */
    public function updateEditableHisto(Request $request, AdminService $adminService)
    {
        $adminService->traiteModification($request->request->all(), HistoriqueBlessure::class);

        return new Response();
    }
}
