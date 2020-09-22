<?php

namespace App\Controller\Admin;

use App\Entity\Coaches;
use App\Form\admin\CoachesType;
use App\Repository\CoachesRepository;
use App\Service\AdminService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

/**
 * @Route("/Admin/coaches")
 */
class CoachesController extends AbstractController
{
    /**
     * @Route("/", name="coaches_index", methods={"GET"})
     */
    public function index(CoachesRepository $coachesRepository): Response
    {
        return $this->render('statbb/admin/coaches/index.html.twig', [
            'coaches' => $coachesRepository->findAll(),
        ]);
    }

    /**
     * @Route("/new", name="coaches_new", methods={"GET","POST"})
     * @param Request $request
     * @param UserPasswordEncoderInterface $encoder
     * @return Response
     */
    public function new(Request $request, UserPasswordEncoderInterface $encoder): Response
    {
        $coach = new Coaches();
        $form = $this->createForm(CoachesType::class, $coach);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();

            $datas = $request->request->get('coaches');
            $coach->setRoles(['role' => 'ROLE_'.$datas['roles']]);

            $encoded = $encoder->encodePassword($coach, $coach->getPasswd());
            $coach->setPasswd($encoded);

            $entityManager->persist($coach);
            $entityManager->flush();

            return $this->redirectToRoute('coaches_index');
        }

        return $this->render('statbb/admin/coaches/new.html.twig', [
            'coach' => $coach,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{coachId}/edit", name="coaches_edit", methods={"GET","POST"})
     */
    public function edit(Request $request, Coaches $coach, UserPasswordEncoderInterface $encoder): Response
    {
        $form = $this->createForm(CoachesType::class, $coach);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $datas = $request->request->get('coaches');
            $coach->setRoles(['role' => 'ROLE_'.$datas['roles']]);

            $coach->setPasswd($encoder->encodePassword($coach, $coach->getPasswd()));

            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('coaches_index');
        }

        return $this->render('statbb/admin/coaches/edit.html.twig', [
            'coach' => $coach,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{coachId}", name="coaches_delete", methods={"DELETE"})
     */
    public function delete(Request $request, Coaches $coach): Response
    {
        if ($this->isCsrfTokenValid('delete'.$coach->getCoachId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($coach);
            $entityManager->flush();
        }

        return $this->redirectToRoute('coaches_index');
    }

    /**
     * @param Request $request
     * @Route("/updateEditableCoach", name="updateEditableCoach", options = { "expose" = true })
     */
    public function updateEditableCoach(Request $request, UserPasswordEncoderInterface $encoder, AdminService $adminService)
    {
        $adminService->traiteModification($request->request->all(), Coaches::class, $encoder);

        return new Response();
    }
}
