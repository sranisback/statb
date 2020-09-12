<?php

namespace App\Controller\Admin;

use App\Entity\GameDataPlayers;
use App\Entity\Players;
use App\Form\admin\PlayersType;
use App\Repository\PlayersRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("Admin/players")
 */
class PlayersController extends AbstractController
{
    /**
     * @Route("/", name="players_index", methods={"GET"})
     */
    public function index(PlayersRepository $playersRepository): Response
    {
        return $this->render('statbb/admin/players/index.html.twig', [
            'players' => $playersRepository->findAll(),
        ]);
    }

    /**
     * @Route("/new", name="players_new", methods={"GET","POST"})
     */
    public function new(Request $request): Response
    {
        $player = new Players();
        $form = $this->createForm(PlayersType::class, $player);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();

            $datas = $request->request->get('players');
            $player->setType($datas['type']);

            /** @var GameDataPlayers $position */
            $position = $entityManager->getRepository(GameDataPlayers::class)->findOneBy(['posId' => $datas['fPos']]);
            $player->setValue($position->getCost());

            $player->setStatus(1);

            $entityManager->persist($player);
            $entityManager->flush();

            return $this->redirectToRoute('players_index');
        }

        return $this->render('statbb/admin/players/new.html.twig', [
            'player' => $player,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{playerId}/edit", name="players_edit", methods={"GET","POST"})
     */
    public function edit(Request $request, Players $player): Response
    {
        $form = $this->createForm(PlayersType::class, $player);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $datas = $request->request->get('players');
            $player->setType($datas['type']);

            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('players_index');
        }

        return $this->render('statbb/admin/players/edit.html.twig', [
            'player' => $player,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{playerId}", name="players_delete", methods={"DELETE"})
     */
    public function delete(Request $request, Players $player): Response
    {
        if ($this->isCsrfTokenValid('delete'.$player->getPlayerId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($player);
            $entityManager->flush();
        }

        return $this->redirectToRoute('players_index');
    }
}
