<?php

namespace App\Controller\Admin;

use App\Entity\Matches;
use App\Form\Admin\MatchesType;
use App\Repository\MatchesRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/Admin/matches")
 */
class MatchesController extends AbstractController
{
    /**
     * @Route("/", name="matches_index", methods={"GET"})
     */
    public function index(MatchesRepository $matchesRepository): Response
    {
        return $this->render('statbb/admin/matches/index.html.twig', [
            'matches' => $matchesRepository->findAll(),
        ]);
    }

    /**
     * @Route("/new", name="matches_new", methods={"GET","POST"})
     */
    public function new(Request $request): Response
    {
        $match = new Matches();
        $form = $this->createForm(MatchesType::class, $match);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($match);
            $entityManager->flush();

            return $this->redirectToRoute('matches_index');
        }

        return $this->render('statbb/admin/matches/new.html.twig', [
            'match' => $match,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{matchId}/edit", name="matches_edit", methods={"GET","POST"})
     */
    public function edit(Request $request, Matches $match): Response
    {
        $form = $this->createForm(MatchesType::class, $match);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('matches_index');
        }

        return $this->render('statbb/admin/matches/edit.html.twig', [
            'match' => $match,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{matchId}", name="matches_delete", methods={"DELETE"})
     */
    public function delete(Request $request, Matches $match): Response
    {
        if ($this->isCsrfTokenValid('delete'.$match->getMatchId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($match);
            $entityManager->flush();
        }

        return $this->redirectToRoute('matches_index');
    }
}
