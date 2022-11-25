<?php

namespace App\Controller;

use App\Entity\ClassementGeneral;
use App\Entity\Coaches;
use App\Entity\Matches;
use App\Entity\Players;
use App\Entity\Teams;
use App\Enum\AnneeEnum;
use App\Service\ClassementService;
use App\Service\EquipeService;
use App\Service\SettingsService;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class ClassementController extends AbstractController
{
    /**
     * @var SettingsService
     */
    private SettingsService $settingsService;

    private ManagerRegistry $doctrine;

    private ClassementService $classementService;

    public function __construct(
        SettingsService $settingsService,
        ManagerRegistry $doctrine,
        ClassementService $classementService
    ) {
        $this->settingsService = $settingsService;
        $this->doctrine = $doctrine;
        $this->classementService = $classementService;
    }

    /**
     * @Route("/classement/general/{annee}/{etiquette}",
     *     defaults={"etiquette"=null}, name="classementgen")
     * @param int $annee
     * @param string|null $etiquette
     * @return Response
     */
    public function classGen(int $annee, ?string $etiquette): Response
    {
        return $this->render(
            'statbb/tabs/ligue/classement.html.twig',
            [
                'classement' => $this->doctrine->getRepository(ClassementGeneral::class)->classementGeneral($annee),
                'annee' => $annee,
                'etiquette' => $etiquette
            ]
        );
    }

    /**
     * @Route("/classement/detail/{annee}", name="classGenDetail")
     * @param int $annee
     * @return Response
     */
    public function classGenDetail(int $annee) : Response
    {
        return $this->render(
            'statbb/tabs/ligue/classementDetail.html.twig',
            [
                'classementDet' => $this->doctrine
                        ->getRepository(ClassementGeneral::class)
                        ->classementGeneralDetail($annee)
            ]
        );
    }

    /**
     * @Route("/classementEquipe/{type}/{limit}/{annee}", defaults={"limit"=0}, name="classementEquipe")
     * @param string $type
     * @param int $limit
     * @param int $annee
     * @return Response
     */
    public function afficheSousClassementsEquipe(string $type, int $limit, int $annee): Response {
        return $this->render(
            'statbb/Stclassement.html.twig',
            $this->classementService->genereClassementEquipes($annee, $type, $limit)
        );
    }

    /**
     * @Route("/classementJoueur/{type}/{limit}/{annee}", defaults={"limit"=0}, name="classementJoueur")
     * @param string $type
     * @param int $limit
     * @param int $annee
     * @return Response
     */
    public function afficheSousClassementJoueur(string $type, int $limit, int $annee): Response {
        return $this->render(
            'statbb/Spclassement.html.twig',
            $this->classementService->genereClassementJoueurs($annee, $type, $limit)
        );
    }

    /**
     * @Route("/totalcas/{annee}", options = { "expose" = true }))
     * @param int $annee
     * @return Response
     */
    public function affichetotalCas(int $annee) : Response
    {
        $totalCas = $this->classementService->totalCas($annee);

        return new Response(
            '<strong>Total : ' . $totalCas['score'] . ' En ' . $totalCas['nbrMatches'] . ' Matches.</strong><br/>
                 <strong>Par Matches :  ' . $totalCas['moyenne'] . '</strong>'
        );
    }

    /**
     * @Route("/cinqDernierMatch")
     * @return Response
     */
    public function cinqDernierMatch(): Response
    {
        return $this->render(
            'statbb/lastfivesmatches.html.twig',
            ['games' => $this->classementService->cinqDerniersMatchsParAnnee($this->settingsService->anneeCourante())]
        );
    }

    /**
     * @Route("/cinqDernierMatchPourEquipe/{teamId}")
     * @param Teams $equipe
     * @return Response
     */
    public function cinqDernierMatchPourEquipe(Teams $equipe) : Response
    {
        return $this->render(
            'statbb/lastfivesmatches.html.twig',
            ['games' => $this->classementService->cinqDerniersMatchsParEquipe($equipe)]
        );
    }

    /**
     * @Route("/tousLesMatchesPourEquipe/{teamId}")
     * @param Teams $equipe
     * @return Response
     */
    public function tousLesMatchesPourEquipe(Teams $equipe) : Response
    {
        return $this->render(
            'statbb/tousLesMatches.html.twig',
            ['games' => $this->doctrine->getRepository(Matches::class)->listeDesMatchs($equipe)]
        );
    }

    /**
     * @Route("/montreLeCimetierre", name="montreLeCimetierre")
     * @return Response
     */
    public function montreLeCimetiere(): Response
    {
        return $this->render(
            'statbb/tabs/ligue/cimetiere.html.twig',
            [
                'joueurCollection' => $this->doctrine->getRepository(Players::class)->mortPourlAnnee(
                    $this->settingsService->anneeCourante()
                ),
            ]
        );
    }

    /**
     * @Route("/montreClassementELO", name="montreClassementELO")
     * @return Response
     */
    public function montreClassementELO(): Response
    {
        return $this->render(
            'statbb/tabs/ligue/classementELO.html.twig',
            [
                'equipeCollection' => $this->doctrine->getRepository(Teams::class)->findBy(
                    ['year' => $this->settingsService->anneeCourante(), 'retired' => false]
                ),
            ]
        );
    }

    /**
     * @Route("/montreConfrontation", name="montreConfrontation")
     * @return Response
     */
    public function afficheConfrontation() : Response
    {
        return $this->render(
            'statbb/tabs/coach/confrontation.html.twig',
            [
                'tableauConfrontation' => $this->classementService->confrontationTousLesCoaches($this->getUser()),
            ]
        );
    }

    /**
     * @route("/ancienClassement/{annee}", name="ancienClassement")
     * @param int $annee
     * @return Response
     */
    public function afficheAncienClassement(int $annee): Response
    {
        $labelAnnee = AnneeEnum::numeroToAnnee();
        return $this->render(
            'statbb/ancienClassement.html.twig',
            ['annee' => $annee, 'etiquette' => $labelAnnee[$annee]]
        );
    }

    /**
     * @route("/listeAnciennesAnnees", name="listeAncienneAnnnee")
     */
    public function listeAncienAnneClassement(): Response
    {
        return $this->render(
            'statbb/tabs/coach/ancienClassement.html.twig',
            ['annee' => $this->settingsService->anneeCourante(), 'label' => AnneeEnum::numeroToAnnee()]
        );
    }

    /**
     * @route("/matchesContreCoach/{coachId}", name="matchesContreCoach")
     * @param Coaches $coachAdverse
     * @return Response
     */
    public function matchesContreCoach(Coaches $coachAdverse): Response
    {
        $coachActif = $this->getUser();

        return $this->render(
            'statbb/matchsContreUnCoach.html.twig',
            [
                'listeMatches' =>
                    $this
                        ->doctrine
                        ->getRepository(Matches::class)
                        ->tousLesMatchsDeDeuxCoach($coachActif, $coachAdverse),
                'contreCoach' => $coachAdverse->getUsername()
            ]
        );
    }

    /**
     * @route("/calculClassementGen/{annee}", name="calcul_classement_gen" )
     * @param int $annee
     * @return RedirectResponse
     */
    public function calculClassementGen(int $annee): RedirectResponse
    {
        $this->classementService->sauvegardeClassementGeneral(
            $this->classementService->toutesLesEquipesPourLeClassementGeneral(
                $annee,
                $this->settingsService->pointsEnCours($annee)
            )
        );

        $labelAnnee = AnneeEnum::numeroToAnnee();

        $this->addFlash('success', 'Classement Calculé! Année: '. $labelAnnee[$annee]);

        return $this->redirectToRoute('index');
    }
}
