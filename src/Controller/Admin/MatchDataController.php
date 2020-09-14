<?php

namespace App\Controller\Admin;

use App\Entity\MatchData;
use App\Form\admin\MatchDataType;
use App\Repository\MatchDataRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("Admin/match/data")
 */
class MatchDataController extends AbstractController
{
    /**
     * @Route("/", name="match_data_index", methods={"GET"})
     */
    public function index(MatchDataRepository $matchDataRepository): Response
    {
        return $this->render('statbb/admin/match_data/index.html.twig', [
            'match_datas' => $matchDataRepository->findAll(),
        ]);
    }

    /**
     * @Route("/new", name="match_data_new", methods={"GET","POST"})
     */
    public function new(Request $request): Response
    {
        $matchDatum = new MatchData();
        $form = $this->createForm(MatchDataType::class, $matchDatum);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($matchDatum);
            $entityManager->flush();

            return $this->redirectToRoute('match_data_index');
        }

        return $this->render('match_data/new.html.twig', [
            'match_datum' => $matchDatum,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}/edit", name="match_data_edit", methods={"GET","POST"})
     */
    public function edit(Request $request, MatchData $matchDatum): Response
    {
        $form = $this->createForm(MatchDataType::class, $matchDatum);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('match_data_index');
        }

        return $this->render('statbb/admin/match_data/edit.html.twig', [
            'match_datum' => $matchDatum,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="match_data_delete", methods={"DELETE"})
     */
    public function delete(Request $request, MatchData $matchDatum): Response
    {
        if ($this->isCsrfTokenValid('delete'.$matchDatum->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($matchDatum);
            $entityManager->flush();
        }

        return $this->redirectToRoute('match_data_index');
    }
}
