<?php

namespace App\Controller;

use App\Entity\Coaches;
use App\Service\DefisService;
use App\Service\SettingsService;

use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;

use Symfony\Component\Serializer\Serializer;
use Symfony\Component\Serializer\Encoder\XmlEncoder;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;

/**
 * Class StatBBController
 * @package App\Controller
 */
class StatBBController extends AbstractController
{
    /**
     * @param  mixed $response
     * @return JsonResponse
     */
    public static function transformeEnJson($response): JsonResponse
    {
        $encoders = array(new XmlEncoder(), new JsonEncoder());
        $normalizers = array(new ObjectNormalizer());

        $serializer = new Serializer($normalizers, $encoders);

        $jsonContent = $serializer->serialize($response, 'json');

        return new JsonResponse($jsonContent);
    }

    /**
     * @Route("/", name="index", options = { "expose" = true })
     * @param SettingsService $settingsService
     * @param DefisService $defisService
     * @return Response
     */
    public function index(SettingsService $settingsService, DefisService $defisService)
    {
        /** @var Coaches $coach */
        $coach = $this->getUser();

        if ($coach != null) {
            $role = $coach->getRoles();

            if ($role['role'] == 'ROLE_ADMIN') {
                if ($settingsService->mettreaJourLaPeriode(date('m/d/Y')) == true) {
                    $this->addFlash('admin', 'Periode Mise à jour');
                };
            }

            foreach ($defisService->lesDefisEnCoursContreLeCoach($settingsService, $coach) as $defisEnCours) {
                $this->addFlash('success', $defisEnCours['par'] . ' a defié ' . $defisEnCours['defiee'] . ' !');
            }
        }
        return $this->render('statbb/front.html.twig');
    }

    /**
     * @Route("/login", name="login", options = { "expose" = true })
     * @return Response
     */
    public function login()
    {
        return $this->render('statbb/front.html.twig');
    }

    /**
     * @Route("/logout", name="logout")
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function logout()
    {
        return $this->redirectToRoute('index');
    }

    /**
     * @Route("/citation", options = { "expose" = true })
     * @param SettingsService $settingsService
     * @return Response
     */
    public function citation(SettingsService $settingsService)
    {
        return $this->render('statbb/citation.html.twig', ['citation' => $settingsService->tirerCitationAuHasard()]);
    }

    /**
     * @Route("/dyk", name="dyk", options = { "expose" = true })
     * @param SettingsService $settingsService
     * @return Response
     */
    public function dyk(SettingsService $settingsService)
    {
        return new Response($settingsService->tirerDYKauHasard());
    }

    /**
     * @Route("/frontUser", name="frontUser", options = { "expose" = true })
     */
    public function frontUser()
    {
        return $this->render('statbb/user.html.twig');
    }

    public function tabCoach()
    {
        return $this->render('statbb/tabs/coach/tabCoach.html.twig');
    }

    public function tabLigue()
    {
        return $this->render('statbb/tabs/ligue/tabLigue.html.twig');
    }

    public function tabParametre()
    {
        return $this->render('statbb/tabs/parametres/tabParametre.html.twig');
    }
}
