<?php


namespace App\Service;

use App\Entity\GameDataPlayers;
use App\Entity\HistoriqueBlessure;
use App\Entity\MatchData;

use App\Entity\Matches;
use App\Entity\Teams;

use App\Entity\Players;
use App\Entity\PlayersSkills;
use App\Entity\GameDataSkills;
use App\Enum\BlessuresEnum;
use App\Factory\PlayerFactory;
use DateTime;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\File\UploadedFile;

class PlayerService
{
    private const BB_2016 = 0;

    private const BB_2020 = 1;

    /**
     * @var EntityManagerInterface
     */
    private EntityManagerInterface $doctrineEntityManager;
    /**
     * @var EquipeService
     */
    private EquipeService $equipeService;
    /**
     * @var MatchDataService
     */
    private MatchDataService $matchDataService;

    private InfosService $infoService;

    public function __construct(
        EntityManagerInterface $doctrineEntityManager,
        EquipeService $equipeService,
        MatchDataService $matchDataService,
        InfosService $infosService
    ) {
        $this->doctrineEntityManager = $doctrineEntityManager;
        $this->equipeService = $equipeService;
        $this->matchDataService = $matchDataService;
        $this->infoService = $infosService;
    }

    public function remplirMatchDataDeLigneAzero(Teams $equipe, Matches $match): void
    {
        foreach ($this->doctrineEntityManager->getRepository(Players::class)->listeDesJoueursActifsPourlEquipe(
            $equipe
        ) as $joueur) {
            $this->matchDataService->creationLigneVideDonneeMatch($joueur, $match);
        }
    }

    /**
     * @param Players $joueur
     * @return array<string,mixed>
     */
    public function statsDuJoueur(Players $joueur): array
    {
        $toutesLesCompsDuJoueur = $this->toutesLesCompsdUnJoueur($joueur);

        $actions = $this->actionsDuJoueur($joueur);

        if (!empty($toutesLesCompsDuJoueur)) {
            $toutesLesCompsDuJoueur = substr($toutesLesCompsDuJoueur, 0, strlen($toutesLesCompsDuJoueur) - 2);
        }

        return ['comp' => $toutesLesCompsDuJoueur, 'actions' => $actions];
    }

    /**
     * @param Players $joueur
     * @return string
     */
    public function toutesLesCompsdUnJoueur(Players $joueur): string
    {
        $toutesLesCompsDuJoueur = $this->listeDesCompdDeBasedUnJoueur($joueur);
        $compsupp = $this->listeDesCompEtSurcoutGagnedUnJoueur($joueur);

        $toutesLesCompsDuJoueur .= $compsupp['compgagnee'];

        $statpec = $this->listenivSpeciauxEtSurcout($joueur);

        $toutesLesCompsDuJoueur .= $statpec['nivspec'];

        if ($joueur->getJournalier() == true) {
            $toutesLesCompsDuJoueur .= '<text class="text-danger">Loner</text>, ';
        }

        return $toutesLesCompsDuJoueur;
    }

    /**
     * @param Players $joueur
     * @return string
     */
    public function listeDesCompdDeBasedUnJoueur(Players $joueur): string
    {
        switch ($joueur->getRuleset()) {
            case self::BB_2016:
                $position = $joueur->getFPos();
                break;
            case self::BB_2020:
                $position = $joueur->getFPosBb2020();
                break;
        }

        if ($position !== null) {
            return $this->listeDesCompdUnePosition($position);
        }

        return '';
    }

    /**
     * @param $position
     * @return string
     */
    public function listeDesCompdUnePosition($position): string
    {
        $listeCompBase = $position->getBaseSkills();
        $listeCompBaseEnHtml = '';

        if (!$listeCompBase->isEmpty()) {
            foreach ($listeCompBase as $comp) {
                $listeCompBaseEnHtml .= '<text class="test-primary">' . $comp->getName() . '</text>, ';
            }
        }

        return $listeCompBaseEnHtml;
    }

    /**
     * @param Players $joueur
     * @return array<string,mixed>
     */
    public function listeDesCompEtSurcoutGagnedUnJoueur(Players $joueur): array
    {
        $coutTotal = 0;
        $listCompGagnee = '';
        foreach ($joueur->getSkills() as $comp) {
            if ($comp->getType() == 'N') {
                $coutTotal += 20_000;
                $listCompGagnee .= '<text class="text-success">' . $comp->getFSkill()->getName() . '</text>, ';
            } else {
                $coutTotal += 30_000;
                $listCompGagnee .= '<text class="text-danger">' . $comp->getFSkill()->getName() . '</text>, ';
            }
        }

        return ['compgagnee' => $listCompGagnee, 'cout' => $coutTotal];
    }

    /**
     * @param Players $joueur
     * @return array<string,mixed>
     */
    public function listenivSpeciauxEtSurcout(Players $joueur): array
    {
        $listSupp = '';
        $cout = 0;

        if ($joueur->getInjNi() > 0) {
            $listSupp .= '<text class="text-danger">+1 Ni</text>, ';
        }

        if ($joueur->getAchMa() > 0) {
            $listSupp .= '<text class="text-success">+1 Ma</text>, ';
            $cout += 30_000;
        }

        if ($joueur->getAchSt() > 0) {
            $listSupp .= '<text class="text-success">+1 St</text>, ';

            $cout += 50_000;
        }

        if ($joueur->getAchAg() > 0) {
            $listSupp .= '<text class="text-success">+1 Ag</text>, ';

            $cout += 40_000;
        }

        if ($joueur->getAchAv() > 0) {
            $listSupp .= '<text class="text-success">+1 Av</text>, ';

            $cout += 30_000;
        }

        return ['nivspec' => $listSupp, 'cout' => $cout];
    }

    /**
     * @param Players $joueur
     * @return array<string,int>
     */
    public function actionsDuJoueur(Players $joueur): array
    {
        $mdata = $this->doctrineEntityManager->getRepository(MatchData::class)->findBy(
            ['fPlayer' => $joueur->getPlayerId()]
        );

        $tcp = 0;
        $ttd = 0;
        $tint = 0;
        $tcas = 0;
        $tmvp = 0;
        $tagg = 0;
        $tMatch = 0;
        $tBonus = 0;

        foreach ($mdata as $game) {
            $tcp += $game->getCp();
            $ttd += $game->getTd();
            $tint += $game->getIntcpt();
            $tcas += ($game->getBh() + $game->getSi() + $game->getKi());
            $tmvp += $game->getMvp();
            $tagg += $game->getAgg();
            $tBonus += $game->getBonusSpp();
            $tMatch++;
        }

        return [
            'NbrMatch' => $tMatch,
            'cp' => $tcp,
            'td' => $ttd,
            'int' => $tint,
            'cas' => $tcas,
            'mvp' => $tmvp,
            'agg' => $tagg,
            'bonus' => $tBonus
        ];
    }

    /**
     * @param Players $joueur
     * @return string
     */
    public function statutDuJoueur(Players $joueur): string
    {
        switch ($joueur->getStatus()) {
            case 7:
                return 'VENDU';

            case 8:
                return 'MORT';

            case 9:
                return 'XP';

            default:
                if ($joueur->getInjRpm() !== 0) {
                    return 'RPM';
                } else {
                    return '';
                }
        }
    }

    /**
     * @param Matches $match
     * @param Players $joueur
     * @return string
     */
    public function actionDuJoueurDansUnMatch(Matches $match, Players $joueur): string
    {
        $actions = '';
        $labelBlessure = (new BlessuresEnum())->numeroToBlessure();

        foreach ($this->doctrineEntityManager->getRepository(MatchData::class)->findBy(
            ['fPlayer' => $joueur->getPlayerId(), 'fMatch' => $match]
        ) as $matchData) {
            $actions .= $this->matchDataService->lectureLignedUnMatch($matchData);
            foreach ($match->getBlessuresMatch() as $blessure) {
                if ($blessure->getPlayer() === $joueur) {
                    $actions .= 'Blessure : ' . $labelBlessure[$blessure->getBlessure()] . ', ';
                }
            }
        }

        return $actions;
    }

    /**
     * @param int $positionId
     * @param int $teamId
     * @param string $nom
     * @param string $numero
     * @return array<string,mixed>
     */
    public function ajoutJoueur(int $positionId, int $teamId, string $nom, string $numero): array
    {
        $position = $this->doctrineEntityManager->getRepository(
            GameDataPlayers::class
        )->findOneBy(['posId' => $positionId]);

        $equipe = $this->doctrineEntityManager->getRepository(Teams::class)->findOneBy(['teamId' => $teamId]);

        $count = 0;

        $joueursParPositionCollection = $this->doctrineEntityManager->getRepository(Players::class)->findBy(
            ['fPos' => $position, 'ownedByTeam' => $equipe]
        );

        foreach ($joueursParPositionCollection as $joueurParPosition) {
            if ($joueurParPosition->getStatus() != 7 && $joueurParPosition->getStatus() != 8) {
                $count++;
            }
        }

        if ($numero === '') {
            $numero = $this->numeroLibreDelEquipe($equipe);
        }

        if ($equipe && $position) {
            if ($equipe->getTreasury() >= $position->getCost()) {
                if ($count < $position->getQty()) {
                    $tresors = $equipe->getTreasury() - $position->getCost();
                    $equipe->setTreasury($tresors);

                    $joueur = PlayerFactory::nouveauJoueur(
                        $position,
                        (int)$numero,
                        $equipe,
                        1,
                        $nom,
                        $this->doctrineEntityManager
                    );

                    $this->infoService->joueurEngage($joueur);

                    $this->doctrineEntityManager->persist($joueur);

                    $equipe->setTv($this->equipeService->tvDelEquipe($equipe, $this));

                    $this->doctrineEntityManager->persist($equipe);
                    $this->doctrineEntityManager->flush();

                    return ['resultat' => 'ok', 'joueur' => $joueur];
                }

                return ['resultat' => "Plus de place"];
            }

            return ['resultat' => "Pas assez d'argent"];
        }

        return ['resultat' => 'erreur'];
    }

    /**
     * @param Teams $equipe
     * @return int
     */
    public function numeroLibreDelEquipe(Teams $equipe): int
    {
        $joueurCollection = $this->doctrineEntityManager->getRepository(
            Players::class
        )->listeDesJoueursPourlEquipe($equipe);

        $numero = 1;

        foreach ($joueurCollection as $joueur) {
            if ($numero == $joueur->getNr()) {
                $numero++;
            } else {
                continue;
            }
        }

        return $numero;
    }

    /**
     * @param Players $joueur
     * @return array<string,mixed>
     */
    public function renvoisOuSuppressionJoueur(Players $joueur): array
    {
        $equipe = $joueur->getOwnedByTeam();
        $position = $joueur->getFPos();
        $effect = "nope";

        if ($equipe && $position) {
            $matchjoues = $this->doctrineEntityManager->getRepository(MatchData::class)->listeDesMatchsdUnJoueur(
                $joueur
            );
            if (!$matchjoues && $joueur->getJournalier() === false) {
                $effect = "rm";
                $equipe->setTreasury($equipe->getTreasury() + $position->getCost());
                $this->doctrineEntityManager->remove($joueur);
                $this->doctrineEntityManager->flush();
            } else {
                $this->doctrineEntityManager->persist($equipe);
                $joueur->setStatus(7);
                $dateSoldFormat = DateTime::createFromFormat("Y-m-d H:i:s", date("Y-m-d H:i:s"));
                if ($dateSoldFormat) {
                    $joueur->setDateSold($dateSoldFormat);
                }
                $this->doctrineEntityManager->persist($joueur);
                $effect = "sld";
            }
            $this->doctrineEntityManager->flush();

            $equipe->setTv($this->equipeService->tvDelEquipe($equipe, $this));

            $this->doctrineEntityManager->persist($equipe);
            $this->doctrineEntityManager->flush();
            $this->doctrineEntityManager->refresh($equipe);

            return [
                'reponse' => $effect,
                'tv' => $equipe->getTv() / 1_000,
                'tresor' => $equipe->getTreasury(),
                'playercost' => $this->valeurDunJoueur($joueur),
            ];
        }

        return ['error' => 'error'];
    }

    /**
     * @param Players $joueur
     * @return int|float
     */
    public function valeurDunJoueur(Players $joueur)
    {
        $position = $joueur->getFPos();
        if ($position !== null) {
            $coutCompetencesGagnee = $this->listeDesCompEtSurcoutGagnedUnJoueur($joueur);
            $coutNiveauSpeciaux = $this->listenivSpeciauxEtSurcout($joueur);

            $coutPosition = $position->getCost();

            if ($this->leJoueurEstDisposable($joueur) || $this->leJoueurEstFanFavorite($joueur)) {
                $coutPosition = 0;
            }

            return $coutPosition + $coutCompetencesGagnee['cout'] + $coutNiveauSpeciaux['cout'];
        }

        return 0;
    }

    /**
     * @param Players $joueur
     * @param GameDataSkills $competenceGagnee
     */
    public function ajoutCompetence(Players $joueur, GameDataSkills $competenceGagnee): void
    {
        $competenceGagneeParLeJoueur = new PlayersSkills();

        $competenceGagneeParLeJoueur->setFPid($joueur);

        $competenceGagneeParLeJoueur->setFSkill($competenceGagnee);

        if ($competenceGagnee->getCat() == 'C') {
            switch ($competenceGagnee->getSkillId()) {
                case 117:
                    $joueur->setAchMa($joueur->getAchMa() + 1);
                    break;
                case 118:
                    $joueur->setAchSt($joueur->getAchSt() + 1);
                    break;
                case 119:
                    $joueur->setAchAg($joueur->getAchAg() + 1);
                    break;
                case 120:
                    $joueur->setAchAv($joueur->getAchAv() + 1);
                    break;
            }
        } else {
            $positionDuJoueur = $joueur->getFPos();

            $normale = '';
            $double = '';

            if ($positionDuJoueur !== null) {
                $double = $positionDuJoueur->getDoub();
                $normale = $positionDuJoueur->getNorm();
            }

            $pos = stripos((string)$normale, (string)$competenceGagnee->getCat());

            if ($pos === false) {
            } else {
                $competenceGagneeParLeJoueur->setType('N');
            }

            $pos = stripos((string)$double, (string)$competenceGagnee->getCat());

            if ($pos === false) {
            } else {
                $competenceGagneeParLeJoueur->setType('D');
            }

            $this->doctrineEntityManager->persist($competenceGagneeParLeJoueur);
            $this->doctrineEntityManager->flush();
        }

        $joueur->setStatus(1);

        $this->doctrineEntityManager->persist($joueur);
        $this->doctrineEntityManager->flush();
    }

    /**
     * @param Teams $equipe
     * @return int
     */
    public function coutTotalJoueurs(Teams $equipe): int
    {
        $players = $this->doctrineEntityManager->getRepository(Players::class)->listeDesJoueursPourlEquipe($equipe);

        $coutTotalJoueur = 0;

        foreach ($players as $joueur) {
            switch ($joueur->getStatus()) {
                case 7:
                case 8:
                    break;
                default:
                    if ($joueur->getInjRpm() == 0) {
                        $coutTotalJoueur += $this->valeurDunJoueur($joueur);
                    }
                    break;
            }
        }

        return (int)$coutTotalJoueur;
    }

    /**
     * @param Teams $equipe
     */
    public function annulerRPMtousLesJoueursDeLequipe(Teams $equipe): void
    {
        foreach ($this->doctrineEntityManager->getRepository(Players::class)->listeDesJoueursPourlEquipe(
            $equipe
        ) as $joueur) {
            $this->annulerRPMunJoueur($joueur);

            $this->doctrineEntityManager->persist($joueur);
        }

        $this->doctrineEntityManager->flush();
    }

    /**
     * @param Players $joueur
     */
    public function annulerRPMunJoueur(Players $joueur): void
    {
        if ($joueur->getInjRpm() > 0) {
            $joueur->setInjRpm($joueur->getInjRpm() - 1);
        }
    }

    /**
     * @param Teams $equipe
     */
    public function controleNiveauDesJoueursDelEquipe(Teams $equipe): void
    {
        $tableComp = [
            0 => 5,
            1 => 15,
            2 => 30,
            3 => 50,
            4 => 75
        ];

        foreach ($this->doctrineEntityManager->getRepository(Players::class)->listeDesJoueursActifsPourlEquipe(
            $equipe
        ) as $joueur) {
            /** @var Players $joueur */
            $spp = $this->xpDuJoueur($joueur);

            $nbrSkill = $this->nbrCompetencesEtAugmentationsGagnee($joueur);

            if ($spp > $tableComp[$nbrSkill]) {
                $joueur->setStatus(9);
            }

            $this->doctrineEntityManager->persist($joueur);
        }
    }

    /**
     * @param Players $joueur
     * @return float|int
     */
    public function xpDuJoueur(Players $joueur)
    {
        $actions = $this->actionsDuJoueur($joueur);

        return $actions['cp'] + ($actions['td'] * 3)
            + ($actions['int'] * 2) + ($actions['cas'] * 2) + ($actions['mvp'] * 5) + ($actions['bonus']);
    }

    /**
     * @param Matches $match
     * @param Teams $equipe
     * @return string
     */
    public function toutesLesActionsDeLequipeDansUnMatch(Matches $match, Teams $equipe): string
    {
        $textAction = '';

        foreach ($this->doctrineEntityManager->getRepository(MatchData::class)->listeDesJoueursdUnMatch(
            $match,
            $equipe
        ) as $listeActionsDunJoueur) {
            $actions = $this->actionDuJoueurDansUnMatch($match, $listeActionsDunJoueur->getFPlayer());

            /** @var MatchData $listeActionsDunJoueur */
            $name = $listeActionsDunJoueur->getFPlayer()->getName();

            if ($actions !== '') {
                if (strlen($name) < 2) {
                    $name = 'Inconnu';
                }

                $actions = $this->actionDuJoueurDansUnMatch($match, $listeActionsDunJoueur->getFPlayer());

                $textAction .= $name .
                    ', ' .
                    $listeActionsDunJoueur->getFPlayer()->getFPos()->getPos() .
                    '(' .
                    $listeActionsDunJoueur->getFPlayer()->getNr() .
                    '): ' .
                    substr(
                        $actions,
                        0,
                        strlen($actions) - 2
                    ) . '<br/>';
            }
        }

        return $textAction;
    }

    /**
     * @param Players $joueur
     * @return int
     */
    public function nbrCompetencesEtAugmentationsGagnee(Players $joueur): int
    {
        $skills = $this->doctrineEntityManager->getRepository(PlayersSkills::class)->findBy(
            ['fPid' => $joueur->getPlayerId()]
        );

        return count($skills) + $joueur->getAchAg() + $joueur->getAchAv()
            + $joueur->getAchMa() + $joueur->getAchSt();
    }

    public function toutesLesBlessureDuMatch(Matches $match): string
    {
        $listeBlessure = '<div class="text-center">';
        $labelBlessure = (new BlessuresEnum())->numeroToBlessure();
        /** @var HistoriqueBlessure $blessure */
        foreach ($match->getBlessuresMatch() as $blessure) {
            $joueur = $blessure->getPlayer();
            if (!empty($joueur)) {
                $position = $joueur->getFPos();
                $equipe = $joueur->getOwnedByTeam();
            }

            if (!empty($joueur) && !empty($position) && !empty($equipe)) {
                $listeBlessure .= $joueur->getNr() . '. '
                    . $joueur->getName() . ', '
                    . $position->getPos() . ', '
                    . $equipe->getName() . ' : '
                    . $labelBlessure[$blessure->getBlessure()] . ' <br/>';
            }
        }

        return $listeBlessure . '</div>';
    }

    /**
     * @param Players $joueur
     * @return bool
     */
    public function leJoueurEstDisposable(Players $joueur): bool
    {
        /** @var GameDataSkills $disposable */
        $disposable = $this->doctrineEntityManager
            ->getRepository(GameDataSkills::class)->findOneBy(['name' => 'Disposable']);

        return !empty($disposable) &&
            in_array($disposable, $joueur->getFPos()->getBaseSkills()->toArray());
    }

    /**
     * @param Players $joueur
     * @return bool
     */
    public function leJoueurEstFanFavorite(Players $joueur): bool
    {
        $playersSkill = false;

        /** @var GameDataSkills $fanFavourite */
        $fanFavourite = $this->doctrineEntityManager
            ->getRepository(GameDataSkills::class)->findOneBy(['name' => 'Fan Favorite']);

        if (!empty($fanFavourite)) {
            /** @var PlayersSkills $playersSkill */
            $playersSkill = $this->doctrineEntityManager
                ->getRepository(PlayersSkills::class)
                ->findOneBy(['fPid' => $joueur->getPlayerId(), 'fSkill' => $fanFavourite->getSkillId()]);
        }

        return !empty($fanFavourite) && $playersSkill != false;
    }

    /**
     * @param Players $players
     * @param PlayerService $playerService
     * @return array
     */
    public function ligneJoueur(array $players): array
    {
        $count = 0;

        /** @var Players $joueur */
        if (!empty($players)) {
            foreach ($players as $joueur) {
                $ficheJoueur = $this->statsDuJoueur($joueur);

                $pdata[$count]['pid'] = $joueur->getPlayerId();
                $pdata[$count]['nbrm'] = $ficheJoueur['actions']['NbrMatch'];
                $pdata[$count]['cp'] = $ficheJoueur['actions']['cp'];
                $pdata[$count]['td'] = $ficheJoueur['actions']['td'];
                $pdata[$count]['int'] = $ficheJoueur['actions']['int'];
                $pdata[$count]['cas'] = $ficheJoueur['actions']['cas'];
                $pdata[$count]['mvp'] = $ficheJoueur['actions']['mvp'];
                $pdata[$count]['agg'] = $ficheJoueur['actions']['agg'];
                $pdata[$count]['bonus'] = $ficheJoueur['actions']['bonus'];
                $pdata[$count]['skill'] = $ficheJoueur['comp'];
                $pdata[$count]['spp'] = $this->xpDuJoueur($joueur);
                $pdata[$count]['cost'] = $this->valeurDunJoueur($joueur);
                $pdata[$count]['status'] = $this->statutDuJoueur($joueur);

                if (!$joueur->getName()) {
                    $joueur->setName('Inconnu');
                }

                $count++;
            }
            return $pdata;
        }
        return [];
    }

    /**
     * @param Players $joueur
     * @return array
     */
    public function listesDesActionsDunJoueurParMatches(
        Players $joueur
    ): array {
        $listeMatches = $this->doctrineEntityManager->getRepository(MatchData::class)->listeDesMatchsdUnJoueur($joueur);
        $count = 0;

        if (!empty($listeMatches)) {
            foreach ($listeMatches as $match) {
                $msdata[$count]["mId"] = $match->getMatchId();
                if (!empty($joueur)) {
                    $actions = $this->actionDuJoueurDansUnMatch($match, $joueur);
                }
                $msdata[$count]["data"] = !empty($actions) ? substr($actions, 0, strlen($actions) - 2) : '';

                $count++;
            }
        }

        if ($count === 0) {
            $msdata[$count]["mId"] = 0;
            $msdata[$count]["data"] = '';
        }
        return array($listeMatches, $msdata);
    }


    /**
     * @param $request
     * @param $joueurId
     */
    public function uploadPhotoJoueur($request, Players $joueur, $photoDirectory): void
    {
        $form = $request->files->all();

        /** @var UploadedFile $photo */
        $photo = $form['joueur_photo_envoi']['photo'];
        $photo->move($photoDirectory, $photo->getClientOriginalName());

        $joueur->setPhoto($photo->getClientOriginalName());

        $this->doctrineEntityManager->persist($joueur);
        $this->doctrineEntityManager->flush();

        $this->doctrineEntityManager->refresh($joueur);
    }

    /**
     * @param GameDataPlayers $position
     */
    public function competencesDunePositon(GameDataPlayers $position)
    {
        $competences = $this->listeDesCompdUnePosition($position);
        $competences = substr($competences, 0, strlen($competences) - 2);

        return $competences;
    }
}
