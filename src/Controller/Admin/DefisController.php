<?php

namespace App\Controller\Admin;

use App\Entity\Defis;
use App\Form\Admin\DefisType;
use App\Repository\DefisRepository;
use App\Service\AdminService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/Admin/defis")
 */
class DefisController extends AbstractController
{
    /**
     * @Route("/", name="defis_index", methods={"GET"})
     */
    public function index(DefisRepository $defisRepository): Response
    {
        return $this->render('statbb/admin/defis/index.html.twig', [
            'defis' => $defisRepository->findAll(),
        ]);
    }

    /**
     * @Route("/new", name="defis_new", methods={"GET","POST"})
     */
    public function new(Request $request): Response
    {
        $defi = new Defis();
        $form = $this->createForm(DefisType::class, $defi);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($defi);
            $entityManager->flush();

            return $this->redirectToRoute('defis_index');
        }

        return $this->render('statbb/admin/defis/new.html.twig', [
            'defi' => $defi,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}/edit", name="defis_edit", methods={"GET","POST"})
     */
    public function edit(Request $request, Defis $defi): Response
    {
        $form = $this->createForm(DefisType::class, $defi);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('defis_index');
        }

        return $this->render('statbb/admin/defis/edit.html.twig', [
            'defi' => $defi,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="defis_delete", methods={"DELETE"})
     */
    public function delete(Request $request, Defis $defi): Response
    {
        if ($this->isCsrfTokenValid('delete'.$defi->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($defi);
            $entityManager->flush();
        }

        return $this->redirectToRoute('defis_index');
    }

    /**
     * @param Request $request
     * @Route("/updateEditableDefis", name="updateEditableDefis", options = { "expose" = true })
     */
    public function updateEditableDefis(Request $request, AdminService $adminService)
    {
        $adminService->traiteModification($request->request->all(), Defis::class);

        return new Response();
    }
}
