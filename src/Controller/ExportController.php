<?php

namespace App\Controller;

use App\Entity\Players;
use App\Entity\Teams;
use App\Service\EquipeService;
use App\Service\PlayerService;
use Dompdf\Dompdf;
use Dompdf\Options;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class ExportController extends AbstractController
{
    /**
     * @Route("/pdfTeam/{id}", name="pdfTeam", options = { "expose" = true })
     * @param PlayerService $playerService
     * @param EquipeService $equipeService
     * @param int $id
     */
    public function pdfTeam(PlayerService $playerService, EquipeService $equipeService, $id)
    {
        /** @var Teams $equipe */
        $equipe = $this->getDoctrine()->getRepository(Teams::class)->find($id);

        $count = 0;

        $pdata = [];
        $pdata[] = [];

        $joueurCollection = $this->getDoctrine()->getRepository(Players::class)->listeDesJoueursActifsPourlEquipe(
            $equipe
        );

        foreach ($joueurCollection as $joueur) {
            $listeCompetence = $playerService->toutesLesCompsdUnJoueur($joueur);
            $actionJoueur = $playerService->actionsDuJoueur($joueur);

            if (!$joueur->getName()) {
                $joueur->setName('Inconnu');
            }

            $pdata[$count]['pid'] = $joueur->getPlayerId();
            $pdata[$count]['nbrm'] = $actionJoueur['NbrMatch'];
            $pdata[$count]['cp'] = $actionJoueur['cp'];
            $pdata[$count]['td'] = $actionJoueur['td'];
            $pdata[$count]['int'] = $actionJoueur['int'];
            $pdata[$count]['cas'] = $actionJoueur['cas'];
            $pdata[$count]['mvp'] = $actionJoueur['mvp'];
            $pdata[$count]['agg'] = $actionJoueur['agg'];
            $pdata[$count]['skill'] = substr($listeCompetence, 0, strlen($listeCompetence) - 2);
            $pdata[$count]['spp'] = $playerService->xpDuJoueur($joueur);
            if ($joueur->getInjRpm() != 0) {
                $pdata[$count]['cost'] = '<s>'.$playerService->valeurDunJoueur($joueur).'</s>';
            } else {
                $pdata[$count]['cost'] = $playerService->valeurDunJoueur($joueur);
            }
            $pdata[$count]['status'] = $playerService->statutDuJoueur($joueur);

            $count++;
        }

        $race = $equipe->getFRace();

        $costRr = $race !== null ? $race->getCostRr() : 0;

        $tdata['playersCost'] = $playerService->coutTotalJoueurs($equipe);
        $tdata['rerolls'] = $equipe->getRerolls() * $costRr;
        $tdata['pop'] = ($equipe->getFf() + $equipe->getFfBought()) * 10_000;
        $tdata['asscoaches'] = $equipe->getAssCoaches() * 10_000;
        $tdata['cheerleader'] = $equipe->getCheerleaders() * 10_000;
        $tdata['apo'] = $equipe->getApothecary() * 50_000;
        $tdata['tv'] = $equipeService->tvDelEquipe($equipe, $playerService);

        $html = $this->renderView(
            'statbb/pdfteam.html.twig',
            [
                'players' => $joueurCollection,
                'team' => $equipe,
                'pdata' => $pdata,
                'tdata' => $tdata,
            ]
        );


        // Configure Dompdf according to your needs
        $pdfOptions = new Options();
        $pdfOptions->set('defaultFont', 'Arial');
        $pdfOptions->setIsRemoteEnabled(true);
        $pdfOptions->setIsHtml5ParserEnabled(true);

        $dompdf = new Dompdf($pdfOptions);

        // Load HTML to Dompdf
        $dompdf->loadHtml($html);

        // (Optional) Setup the paper size and orientation 'portrait' or 'portrait'
        $dompdf->setPaper('A4', 'landscape');

        // Render the HTML as PDF
        $dompdf->render();

        $dompdf->stream($equipe->getName().'.pdf', ["Attachment" => true]);
    }
}
