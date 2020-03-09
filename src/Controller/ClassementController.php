<?php

namespace App\Controller;

use App\Entity\Coaches;
use App\Entity\Matches;
use App\Entity\Players;
use App\Entity\Teams;
use App\Enum\AnneeEnum;
use App\Service\ClassementService;
use App\Service\EquipeService;
use App\Service\SettingsService;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class ClassementController extends AbstractController
{
    /**
     * @var \App\Service\SettingsService
     */
    private \App\Service\SettingsService $settingsService;

    public function __construct(SettingsService $settingsService)
    {
        $this->settingsService = $settingsService;
    }

    /**
     * @Route("/classement/general/{annee}/{etiquette}",
     *     defaults={"etiquette"=null}, name="classementgen", options = { "expose" = true })
     * @param int $annee
     * @param string $etiquette
     * @return Response
     */
    public function classGen(int $annee, string $etiquette): \Symfony\Component\HttpFoundation\Response
    {
        return $this->render(
            'statbb/tabs/ligue/classement.html.twig',
            [
                'classement' => $this->getDoctrine()->getRepository(Teams::class)->classement(
                    $annee
                ),
                'annee' => $annee,
                'etiquette' => $etiquette
            ]
        );
    }

    /**
     * @Route("/classement/detail/{annee}", name="classGenDetail", options = { "expose" = true })
     * @param ClassementService $classementService
     * @return Response
     */
    public function classGenDetail(ClassementService $classementService, int $annee)
    : \Symfony\Component\HttpFoundation\Response
    {
        return $this->render(
            'statbb/tabs/ligue/classementDetail.html.twig',
            [
                'classementDet' => $classementService->classementDetail($annee)
            ]
        );
    }

    /**
     * @Route("/classementEquipe/{type}/{limit}/{annee}", defaults={"limit"=0}, name="classementEquipe")
     * @param ClassementService $classementService
     * @param string $type
     * @param int $limit
     * @param int $annee
     * @return Response
     */
    public function afficheSousClassementsEquipe(
        ClassementService $classementService,
        string $type,
        int $limit,
        int $annee
    ): \Symfony\Component\HttpFoundation\Response {
        $sousClassement = $classementService->genereClassementEquipes(
            $annee,
            $type,
            $limit
        );

        return $this->render('statbb/Stclassement.html.twig', $sousClassement);
    }

    /**
     * @Route("/classementJoueur/{type}/{limit}/{annee}", defaults={"limit"=0}, name="classementJoueur")
     * @param ClassementService $classementService
     * @param string $type
     * @param int $limit
     * @param int $annee
     * @return Response
     */
    public function afficheSousClassementJoueur(
        ClassementService $classementService,
        string $type,
        int $limit,
        int $annee
    ): \Symfony\Component\HttpFoundation\Response {
        $sousClassement = $classementService->genereClassementJoueurs(
            $annee,
            $type,
            $limit
        );

        return $this->render('statbb/Spclassement.html.twig', $sousClassement);
    }

    /**
     * @Route("/totalcas/{annee}", options = { "expose" = true })
     */
    public function affichetotalCas(ClassementService $classementService, int $annee)
    : \Symfony\Component\HttpFoundation\Response
    {
        $totalCas = $classementService->totalCas($annee);

        return new Response(
            '<strong>Total : ' . $totalCas['score'] . ' En ' . $totalCas['nbrMatches'] . ' Matches.</strong><br/>
                 <strong>Par Matches :  ' . $totalCas['moyenne'] . '</strong>'
        );
    }

    /**
     * @Route("/cinqDernierMatch/", options = { "expose" = true })
     * @param ClassementService $classementService
     * @return Response
     */
    public function cinqDernierMatch(ClassementService $classementService): \Symfony\Component\HttpFoundation\Response
    {
        return $this->render(
            'statbb/lastfivesmatches.html.twig',
            ['games' => $classementService->cinqDerniersMatchsParAnnee($this->settingsService->anneeCourante())]
        );
    }

    /**
     * @Route("/cinqDernierMatchPourEquipe/{equipeId}", options = { "expose" = true })
     * @param ClassementService $classementService
     * @param integer $equipeId
     * @return Response
     */
    public function cinqDernierMatchPourEquipe(ClassementService $classementService, int $equipeId)
    : \Symfony\Component\HttpFoundation\Response
    {
        return $this->render(
            'statbb/lastfivesmatches.html.twig',
            ['games' => $classementService->cinqDerniersMatchsParEquipe($equipeId)]
        );
    }

    /**
     * @Route("/montreLeCimetierre", name="montreLeCimetierre", options = { "expose" = true })
     * @return Response
     */
    public function montreLeCimetiere(): \Symfony\Component\HttpFoundation\Response
    {
        return $this->render(
            'statbb/tabs/ligue/cimetiere.html.twig',
            [
                'joueurCollection' => $this->getDoctrine()->getRepository(players::class)->mortPourlAnnee(
                    $this->settingsService->anneeCourante()
                ),
            ]
        );
    }

    /**
     * @Route("/montreClassementELO", name="montreClassementELO", options = { "expose" = true })
     * @return Response
     */
    public function montreClassementELO(): \Symfony\Component\HttpFoundation\Response
    {
        return $this->render(
            'statbb/tabs/ligue/classementELO.html.twig',
            [
                'equipeCollection' => $this->getDoctrine()->getRepository(Teams::class)->findBy(
                    ['year' => $this->settingsService->anneeCourante()]
                ),
            ]
        );
    }

    /**
     * @Route("/montreConfrontation", name="montreConfrontation", options = { "expose" = true })
     * @param ClassementService $classementService
     * @param EquipeService $equipeService
     * @return Response
     */
    public function afficheConfrontation(ClassementService $classementService, EquipeService $equipeService)
    : \Symfony\Component\HttpFoundation\Response
    {
        /** @var Coaches $coach */
        $coach = $this->getUser();

        return $this->render(
            'statbb/tabs/coach/confrontation.html.twig',
            [
                'tableauConfrontation' => $classementService->confrontationTousLesCoaches(
                    $coach,
                    $equipeService
                ),
            ]
        );
    }

    /**
     * @route("/ancienClassement/{annee}", name="ancienClassement")
     * @param int $annee
     * @return Response
     */
    public function afficheAncienClassement(int $annee): \Symfony\Component\HttpFoundation\Response
    {
        $labelAnnee = (new AnneeEnum)->numeroToAnnee();
        return $this->render(
            'statbb/ancienClassement.html.twig',
            ['annee' => $annee, 'etiquette' => $labelAnnee[$annee]]
        );
    }

    /**
     * @route("/listeAnciennesAnnees", name="listeAncienneAnnnee")
     */
    public function listeAncienAnneClassement(): \Symfony\Component\HttpFoundation\Response
    {
        return $this->render(
            'statbb/tabs/coach/ancienClassement.html.twig',
            ['annee' => $this->settingsService->anneeCourante(), 'label' => (new AnneeEnum)->numeroToAnnee()]
        );
    }

    /**
     * @route("/matchesContreCoach/{coachId}", name="matchesContreCoach")
     * @param int $coachId
     * @return Response
     */
    public function matchesContreCoach(int $coachId): \Symfony\Component\HttpFoundation\Response
    {
        $coachActif = $this->getUser();
        /** @var Coaches $coachAdverse */
        $coachAdverse = $this->getDoctrine()->getRepository(Coaches::class)->findOneBy(['coachId' => $coachId]);

        return $this->render(
            'statbb/matchsContreUnCoach.html.twig',
            [
                'listeMatches' =>
                    $this
                        ->getDoctrine()
                        ->getRepository(Matches::class)
                        ->tousLesMatchsDeDeuxCoach($coachActif, $coachAdverse),
                'contreCoach' => $coachAdverse->getName()
            ]
        );
    }
}
