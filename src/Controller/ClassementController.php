<?php

namespace App\Controller;

use App\Entity\Players;
use App\Entity\Teams;
use App\Service\ClassementService;
use App\Service\SettingsService;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class ClassementController extends AbstractController
{
    private $settingsService;

    public function __construct(SettingsService $settingsService)
    {
        $this->settingsService = $settingsService;
    }

    /**
     * @Route("/classement/general/", name="classementgen", options = { "expose" = true })
     * @return Response
     */
    public function classGen()
    {
        return $this->render(
            'statbb/tabs/ligue/classement.html.twig',
            [
                'classement' => $this->getDoctrine()->getRepository(Teams::class)->classement(
                    $this->settingsService->anneeCourante(),
                    0
                ),
            ]
        );
    }

    /**
     * @Route("/classement/{type}/{teamorplayer}/{limit}", name="classement", options = { "expose" = true })
     * @param ClassementService $classementService
     * @param string $type
     * @param string $teamorplayer
     * @param int $limit
     * @return Response
     */
    public function afficheSousClassements(ClassementService $classementService, $type, $teamorplayer, $limit)
    {
        $sousClassement = $classementService->sousClassements(
            $this->settingsService->anneeCourante(),
            $type,
            $teamorplayer,
            $limit
        );

        if ($teamorplayer == 'player') {
            return $this->render('statbb/Spclassement.html.twig', $sousClassement);
        } else {
            return $this->render('statbb/Stclassement.html.twig', $sousClassement);
        }
    }

    /**
     * @Route("/totalcas", options = { "expose" = true })
     */
    public function affichetotalCas(ClassementService $classementService)
    {
        $totalCas = $classementService->totalCas($this->settingsService->anneeCourante());

        return new Response(
            '<strong>Total : '.$totalCas['score'].' En '.$totalCas['nbrMatches'].' Matches.</strong><br/>
                 <strong>Par Matches :  '.$totalCas['moyenne'].'</strong>'
        );
    }

    /**
     * @Route("/cinqDernierMatch/", options = { "expose" = true })
     * @param ClassementService $classementService
     * @return Response
     */
    public function cinqDernierMatch(ClassementService $classementService)
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
    public function cinqDernierMatchPourEquipe(ClassementService $classementService, $equipeId)
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
    public function montreLeCimetiere()
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
    public function montreClassementELO()
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
}
