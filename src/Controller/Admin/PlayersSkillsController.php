<?php

namespace App\Controller\Admin;

use App\Entity\PlayersSkills;
use App\Form\Admin\PlayersSkillsType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("Admin/players/skills")
 */
class PlayersSkillsController extends AbstractController
{
    /**
     * @Route("/", name="players_skills_index", methods={"GET"})
     */
    public function index(): Response
    {
        $playersSkills = $this->getDoctrine()
            ->getRepository(PlayersSkills::class)
            ->findAll();

        return $this->render('statbb/admin/players_skills/index.html.twig', [
            'players_skills' => $playersSkills,
        ]);
    }

    /**
     * @Route("/new", name="players_skills_new", methods={"GET","POST"})
     */
    public function new(Request $request): Response
    {
        $playersSkill = new PlayersSkills();
        $form = $this->createForm(PlayersSkillsType::class, $playersSkill);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($playersSkill);
            $entityManager->flush();

            return $this->redirectToRoute('players_skills_index');
        }

        return $this->render('statbb/admin/players_skills/new.html.twig', [
            'players_skill' => $playersSkill,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}/edit", name="players_skills_edit", methods={"GET","POST"})
     */
    public function edit(Request $request, PlayersSkills $playersSkill): Response
    {
        $form = $this->createForm(PlayersSkillsType::class, $playersSkill);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('players_skills_index');
        }

        return $this->render('statbb/admin/players_skills/edit.html.twig', [
            'players_skill' => $playersSkill,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="players_skills_delete", methods={"DELETE"})
     */
    public function delete(Request $request, PlayersSkills $playersSkill): Response
    {
        if ($this->isCsrfTokenValid('delete'.$playersSkill->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($playersSkill);
            $entityManager->flush();
        }

        return $this->redirectToRoute('players_skills_index');
    }
}
