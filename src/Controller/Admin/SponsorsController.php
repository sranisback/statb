<?php

namespace App\Controller\Admin;

use App\Entity\Sponsors;
use App\Form\Admin\SponsorsType;
use App\Repository\SponsorsRepository;
use App\Service\AdminService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("Admin/sponsors")
 */
class SponsorsController extends AbstractController
{
    /**
     * @Route("/", name="sponsors_index", methods={"GET"})
     */
    public function index(SponsorsRepository $sponsorsRepository): Response
    {
        return $this->render('statbb/admin/sponsors/index.html.twig', [
            'sponsors' => $sponsorsRepository->findAll(),
        ]);
    }

    /**
     * @Route("/new", name="sponsors_new", methods={"GET","POST"})
     */
    public function new(Request $request, SponsorsRepository $sponsorsRepository): Response
    {
        $sponsor = new Sponsors();
        $form = $this->createForm(SponsorsType::class, $sponsor);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $sponsorsRepository->add($sponsor, true);

            return $this->redirectToRoute('sponsors_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('statbb/admin/sponsors/new.html.twig', [
            'sponsor' => $sponsor,
            'form' => $form,
        ]);
    }

    /**
     * @Route("/{id}/edit", name="sponsors_edit", methods={"GET","POST"})
     */
    public function edit(Request $request, Sponsors $sponsor, SponsorsRepository $sponsorsRepository): Response
    {
        $form = $this->createForm(SponsorsType::class, $sponsor);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $sponsorsRepository->add($sponsor, true);

            return $this->redirectToRoute('sponsors_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('statbb/admin/sponsors/edit.html.twig', [
            'sponsor' => $sponsor,
            'form' => $form,
        ]);
    }

    /**
     * @Route("/{id}", name="sponsors_delete", methods={"DELETE"})
     */
    public function delete(Request $request, Sponsors $sponsor, SponsorsRepository $sponsorsRepository): Response
    {
        if ($this->isCsrfTokenValid('delete'.$sponsor->getId(), $request->request->get('_token'))) {
            $sponsorsRepository->remove($sponsor, true);
        }

        return $this->redirectToRoute('sponsors_index', [], Response::HTTP_SEE_OTHER);
    }

    /**
     * @param Request $request
     * @param AdminService $adminService
     * @return Response
     * @Route("/updateEditableSponsors", name="updateEditableSponsors", methods={"POST"}, options = { "expose" = true })
     */
    public function updateEditableSponsors(Request $request, AdminService $adminService) : Response
    {
        $adminService->traiteModification($request->request->all(), Sponsors::class);

        return new Response();
    }
}
