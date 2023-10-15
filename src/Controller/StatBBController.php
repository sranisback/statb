<?php

namespace App\Controller;

use App\Entity\Coaches;
use App\Entity\GameDataPlayers;
use App\Entity\GameDataSkills;
use App\Entity\Players;
use App\Entity\PlayersIcons;
use App\Enum\RulesetEnum;
use App\Service\CitationService;
use App\Service\DefisService;
use App\Service\SettingsService;
use App\Tools\randomNameGenerator;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\Encoder\XmlEncoder;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use Symfony\Component\Serializer\Serializer;

class StatBBController extends AbstractController
{
    /**
     * @param  mixed $response
     * @return JsonResponse
     */
    public static function transformeObjetEnJsonResponse($response): JsonResponse
    {
        $encoders = [new XmlEncoder(), new JsonEncoder()];
        $normalizers = [new ObjectNormalizer()];

        $serializer = new Serializer($normalizers, $encoders);

        return new JsonResponse($serializer->serialize($response, 'json'));
    }

    /**
     * @Route("/", name="index")
     * @param SettingsService $settingsService
     * @param DefisService $defisService
     * @return Response
     */
    public function index(SettingsService $settingsService, DefisService $defisService) : Response
    {
        /** @var Coaches $coach */
        $coach = $this->getUser();

        if ($coach != null) {
            $role = $coach->getRoles();

            if ($role['role'] == 'ROLE_ADMIN' && $settingsService->mettreaJourLaPeriode(date('m/d/Y'))) {
                $this->addFlash('admin', 'Periode Mise à jour');
            }

            foreach ($defisService->lesDefisEnCoursContreLeCoach($coach) as $defisEnCours) {
                $this->addFlash('success', $defisEnCours['par'] . ' a defié ' . $defisEnCours['defiee'] . ' !');
            }
        }
        return $this->render('statbb/front.html.twig', ['annee' => $settingsService->anneeCourante()]);
    }

    /**
     * @Route("/login", name="login")
     * @return Response
     */
    public function login(SettingsService $settingsService): Response
    {
        return $this->render('statbb/front.html.twig', ['annee' => $settingsService->anneeCourante()]);
    }

    /**
     * @Route("/logout", name="logout")
     * @return RedirectResponse
     */
    public function logout(): RedirectResponse
    {
        return $this->redirectToRoute('index');
    }

    /**
     * @Route("/citation")
     * @param CitationService $citationService
     * @return Response
     */
    public function citation(CitationService $citationService): Response
    {
        return $this->render('statbb/citation.html.twig', ['citation' => $citationService->tirerCitationAuHasard()]);
    }

    /**
     * @Route("/dyk", name="dyk")
     * @param SettingsService $settingsService
     * @return Response
     */
    public function dyk(SettingsService $settingsService): Response
    {
        return new Response($settingsService->tirerDYKauHasard());
    }

    /**
     * @Route("/frontUser", name="frontUser")
     */
    public function frontUser(): Response
    {
        return $this->render('statbb/user.html.twig');
    }

    /**
     * @Route("/tabCoach", name="tabCoach")
     */
    public function tabCoach(): Response
    {
        return $this->render('statbb/tabs/coach/tabCoach.html.twig');
    }

    /**
     * @Route("/tabLigue", name="tabLigue")
     */
    public function tabLigue(SettingsService $settingsService): Response
    {
        return $this->render('statbb/tabs/ligue/tabLigue.html.twig', ['annee' => $settingsService->anneeCourante()]);
    }

    public function tabParametre(): Response
    {
        return $this->render('statbb/tabs/parametres/tabParametre.html.twig');
    }

    /**
     * @Route("/testIcons")
     */
    public function testIcon(): Response
    {
        $icons = $this->getDoctrine()->getRepository(PlayersIcons::class)->findAll();

        return $this->render('statbb/testIcon.html.twig', ['icons' => $icons]);
    }

    /**
     * @Route("/attributIconManquante")
     */
    public function attributIconManquante(): Response
    {
        /** @var Players $joueur */
        foreach ($this->getDoctrine()->getRepository(Players::class)->findBy(['Ruleset' => RulesetEnum::BB_2016]) as $joueur) {
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
     * @Route("/attributIconManquanteBb2020")
     */
    public function attributIconManquanteBb2020(): Response
    {
        /** @var Players $joueur */
        foreach ($this->getDoctrine()->getRepository(Players::class)->findBy(['Ruleset' => RulesetEnum::BB_2020]) as $joueur) {
            $icon = $joueur->getIcon();
            if (!empty($icon) && $icon->getIconName() === 'nope') {
                /** @var PlayersIcons[] $iconesPositions */
                $iconesPositions = $this
                    ->getDoctrine()
                    ->getRepository(PlayersIcons::class)->findBy(['positionBb2020' => $joueur->getFPosBb2020()]);
                if(count($iconesPositions)> 0) {
                    $joueur->setIcon($iconesPositions[ rand(0, count($iconesPositions) - 1)]);
                    $this->getDoctrine()->getManager()->persist($joueur);
                    $this->getDoctrine()->getManager()->flush();
                }
            }
        }
        return new Response('ok');
    }


    /**
     * @Route("/genereNomManquant")
     */
    public function genereNomManquant(): Response
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

    /**
     * @Route("/majBaseSkill")
     */
    public function majBaseSkills(): Response
    {
        /** @var GameDataPlayers $position */
        foreach ($this->getDoctrine()->getRepository(GameDataPlayers::class)->findAll() as $position) {
            foreach (explode(',', $position->getSkills()) as $skillId) {
                $skill = $this->getDoctrine()
                    ->getRepository(GameDataSkills::class)
                    ->findOneBy(['skillId' => $skillId]);
                if (!(empty($skill))) {
                    $position->addBaseSkill($skill);

                    $this->getDoctrine()->getManager()->persist($position);

                    $this->getDoctrine()->getManager()->flush();
                }
            }
        }
        return new Response('ok');
    }
}
