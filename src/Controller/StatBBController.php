<?php

namespace App\Controller;

use App\Entity\Coaches;
use App\Entity\Players;
use App\Entity\PlayersIcons;
use App\Service\DefisService;
use App\Service\SettingsService;

use App\Tools\randomNameGenerator;
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

            if ($role['role'] == 'ROLE_ADMIN' && $settingsService->mettreaJourLaPeriode(date('m/d/Y')) == true) {
                $this->addFlash('admin', 'Periode Mise à jour');;
            }

            foreach ($defisService->lesDefisEnCoursContreLeCoach($settingsService, $coach) as $defisEnCours) {
                $this->addFlash('success', $defisEnCours['par'] . ' a defié ' . $defisEnCours['defiee'] . ' !');
            }
        }
        return $this->render('statbb/front.html.twig', ['annee' => $settingsService->anneeCourante()]);
    }

    /**
     * @Route("/login", name="login", options = { "expose" = true })
     * @return Response
     */
    public function login(SettingsService $settingsService)
    {
        return $this->render('statbb/front.html.twig', ['annee' => $settingsService->anneeCourante()]);
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

    /**
     * @Route("/tabCoach", name="tabCoach")
     */
    public function tabCoach()
    {
        return $this->render('statbb/tabs/coach/tabCoach.html.twig');
    }

    /**
     * @Route("/tabLigue", name="tabLigue")
     */
    public function tabLigue(SettingsService $settingsService)
    {
        return $this->render('statbb/tabs/ligue/tabLigue.html.twig', ['annee' => $settingsService->anneeCourante()]);
    }

    public function tabParametre()
    {
        return $this->render('statbb/tabs/parametres/tabParametre.html.twig');
    }

    /**
     * @Route("/testIcons")
     */
    public function testIcon()
    {
        $icons = $this->getDoctrine()->getRepository(PlayersIcons::class)->findAll();

        return $this->render('statbb/testIcon.html.twig', ['icons' => $icons]);
    }

    /**
     * @Route("/attributIconManquante")
     */
    public function attributIconManquante()
    {
        /** @var Players $joueur */
        foreach ($this->getDoctrine()->getRepository(Players::class)->findAll() as $joueur) {
            $icon = $joueur->getIcon();
            if (!empty($icon) && $icon->getIconName() === 'nope') {
                /** @var PlayersIcons[] $iconesPositions */
                $iconesPositions = $this
                    ->getDoctrine()
                    ->getRepository(PlayersIcons::class)->findBy(['position' => $joueur->getFPos()]);
                $joueur->setIcon($iconesPositions[ rand(0, count($iconesPositions) - 1)]);
                $this->getDoctrine()->getManager()->persist($joueur);
                $this->getDoctrine()->getManager()->flush();
            }
        }
        return new Response('ok');
    }

    /**
     * @Route("/genereNomManquant")
     */
    public function genereNomManquant()
    {
        /** @var Players $joueur */
        foreach ($this->getDoctrine()->getRepository(Players::class)->findAll() as $joueur) {
            if ($joueur->getName() === '' || $joueur->getName() === null) {
                $generateurNom = new randomNameGenerator();
                $nom = $generateurNom->generateNames(1);
                $joueur->setName($nom[0]);

                $this->getDoctrine()->getManager()->persist($joueur);

                $this->getDoctrine()->getManager()->flush();
            }
        }
        return new Response('ok');
    }
}
