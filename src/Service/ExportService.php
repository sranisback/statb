<?php


namespace App\Service;


use App\Entity\Players;
use App\Entity\Teams;
use Doctrine\ORM\EntityManagerInterface;

class ExportService
{
    /**
     * @var EntityManagerInterface
     */
    private EntityManagerInterface $doctrineEntityManager;

    private PlayerService $playerService;

    private InducementService $inducementService;

    private EquipeGestionService $equipeGestionService;

    private EquipeService $equipeService;

    public function __construct(
        EntityManagerInterface $doctrineEntityManager,
        PlayerService $playerService,
        InducementService $inducementService,
        EquipeGestionService $equipeGestionService,
        EquipeService $equipeService
    ) {
        $this->doctrineEntityManager = $doctrineEntityManager;
        $this->playerService = $playerService;
        $this->inducementService = $inducementService;
        $this->equipeGestionService = $equipeGestionService;
        $this->equipeService = $equipeService;
    }

    /**
     * @throws \Exception
     */
    public function generatePdf(Teams $equipe): array
    {
        $count = 0;

        $pdata = [];
        $pdata[] = [];

        $joueurs = $this->equipeService->getListActivePlayers($equipe);

        /** @var Players $joueur */
        foreach ($joueurs as $joueur) {
            $listeCompetence = $this->playerService->toutesLesCompsdUnJoueur($joueur);
            $actionJoueur = $this->playerService->actionsDuJoueur($joueur);

            if (!$joueur->getName()) {
                $joueur->setName('Inconnu');
            }

            if (empty($joueur->getSppDepense())) {
                $joueur->setSppDepense(0);
            }

            $pdata[$count]['pid'] = $joueur->getPlayerId();
            $pdata[$count]['nbrm'] = $actionJoueur['NbrMatch'];
            $pdata[$count]['cp'] = $actionJoueur['cp'];
            $pdata[$count]['td'] = $actionJoueur['td'];
            $pdata[$count]['int'] = $actionJoueur['int'];
            $pdata[$count]['cas'] = $actionJoueur['cas'];
            $pdata[$count]['mvp'] = $actionJoueur['mvp'];
            $pdata[$count]['agg'] = $actionJoueur['agg'];
            $pdata[$count]['exp'] = $actionJoueur['exp'];
            $pdata[$count]['bonusXP'] = $actionJoueur['bonus'];
            $pdata[$count]['skill'] = substr($listeCompetence, 0, strlen($listeCompetence) - 2);
            $pdata[$count]['spp'] = $this->playerService->xpDuJoueur($joueur);
            if ($joueur->getInjRpm() != 0) {
                $pdata[$count]['cost'] = '<s>' . $this->playerService->valeurDunJoueur($joueur) . '</s>';
            } else {
                $pdata[$count]['cost'] = $this->playerService->valeurDunJoueur($joueur);
            }
            $pdata[$count]['status'] = $this->playerService->statutDuJoueur($joueur);

            $count++;
        }

        $tdata = $this->inducementService->valeurInducementDelEquipe($equipe);
        $tdata['playersCost'] = $this->playerService->coutTotalJoueurs($equipe);
        $tdata['tv'] = $this->equipeGestionService->tvDelEquipe($equipe);

        $compteur = $this->equipeService->compteLesjoueurs($equipe);

        return [
            'players' => $joueurs,
            'team' => $equipe,
            'pdata' => $pdata,
            'tdata' => $tdata,
            'nom' => $equipe->getName(),
            'compteur' => $compteur
        ];
    }
}