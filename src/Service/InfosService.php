<?php


namespace App\Service;

use App\Entity\DeadPlayerInfo;
use App\Entity\Defis;
use App\Entity\Infos;
use App\Entity\Matches;
use App\Entity\Players;
use App\Entity\Primes;
use App\Entity\Teams;
use App\Enum\RulesetEnum;
use Doctrine\ORM\EntityManagerInterface;
use Nette\Utils\DateTime;
use Symfony\Component\DependencyInjection\ParameterBag\ContainerBagInterface;

class InfosService
{
    /**
     * @var EntityManagerInterface
     */
    private $doctrineEntityManager;

    /**
     * @var string
     */
    private $urlPrefix;

    private const TEAM_URL = '/team/';
    private const HREF = '<a href="';

    public function __construct(EntityManagerInterface $doctrineEntityManager, ContainerBagInterface $params)
    {
        $this->doctrineEntityManager = $doctrineEntityManager;
        $this->urlPrefix = $params->get('env') == 'dev' ? '' : '/statb/public';
    }

    /**
     * @param string $text
     * @return Infos
     */
    public function publierUnMessage(string $text) : Infos
    {
        $message = new Infos();
        $message->setMessages($text);
        $message->setDate(DateTime::createFromFormat("Y-m-d H:i:s", date("Y-m-d H:i:s")));

        $this->doctrineEntityManager->persist($message);
        $this->doctrineEntityManager->flush();

        return $message;
    }
    
    /**
     * @param Teams $equipe
     * @return Infos
     */
    public function equipeEstCree(Teams $equipe): Infos
    {
        return $this->publierUnMessage(
            $equipe->getOwnedByCoach()->getUsername() .
            ' a crée l\'équipe <a href="' . $this->urlPrefix . self::TEAM_URL . $equipe->getTeamId() .
            '">' . $equipe->getName() . '</a>' .
            '(' . RulesetEnum::getRaceFromEquipeByRuleset($equipe)->getName() .
            ')'
        );
    }

    /**
     * @param Players $joueur
     * @return Infos
     */
    public function joueurEngage(Players $joueur): Infos
    {
        return $this->publierUnMessage(
            $joueur->getName() . ', ' . RulesetEnum::getPositionFromPlayerByRuleset($joueur)->getPos() . ' ' .
            RulesetEnum::getRaceFromJoueurByRuleset($joueur)->getName() .
            ' a été engagé par <a href="' . $this->urlPrefix . self::TEAM_URL .
            $joueur->getOwnedByTeam()->getTeamId() . '">' .
            $joueur->getOwnedByTeam()->getName() . '</a>' .
            ' de ' . $joueur->getOwnedByTeam()->getOwnedByCoach()->getUsername()
        );
    }

    /**
     * @param Matches $matches
     * @return Infos
     */
    public function matchEnregistre(Matches $matches): Infos
    {
        return $this->publierUnMessage(
            'Match(' . $matches->getMatchId() . '): ' .
            $matches->getTeam1()->getName() . ' VS ' . $matches->getTeam2()->getName() . ' enregistré. ' .
            self::HREF . $this->urlPrefix . '/match/' . $matches->getMatchId() . '">voir</a>'
        );
    }

    /**
     * @param Players $joueur
     * @return Infos
     */
    public function mortDunJoueur(Players $joueur): Infos
    {
        $titreJoueur = $joueur->getName() . ', ' .  RulesetEnum::getPositionFromPlayerByRuleset($joueur)->getPos()
            . ' '  . RulesetEnum::getRaceFromJoueurByRuleset($joueur)->getName()
            . ' de <a href="' . $this->urlPrefix . self::TEAM_URL . $joueur->getOwnedByTeam()->getTeamId() . '">'
            . $joueur->getOwnedByTeam()->getName() . '</a>';

        return $this->publierUnMessage($this->genererPhrase($titreJoueur));
    }

    private function genererPhrase($titreJoueur)
    {
        $listePhrases = $this->doctrineEntityManager->getRepository(DeadPlayerInfo::class)->findAll();

        if($listePhrases) {
            $nbrAuHasard = rand(0, count($listePhrases) - 1);

            $phrase = str_replace("*", $titreJoueur, $listePhrases[$nbrAuHasard]->getPhrase());

            return  $phrase;
        }

        return $titreJoueur . '</a> est mort !';

    }

    /**
     * @param Defis $defis
     * @return Infos
     */
    public function defisEstLance(Defis $defis): Infos
    {
        return $this->publierUnMessage(
            self::HREF . $this->urlPrefix . self::TEAM_URL . $defis->getEquipeOrigine()->getTeamId() . '">'
            . $defis->getEquipeOrigine()->getName() . '</a> ('
            .  RulesetEnum::getRaceFromEquipeByRuleset($defis->getEquipeOrigine())->getName()
            . ') défie <a href="' . $this->urlPrefix . self::TEAM_URL . $defis->getEquipeDefiee()->getTeamId() . '">'
            . $defis->getEquipeDefiee()->getName() . '</a> ('
            . RulesetEnum::getRaceFromEquipeByRuleset($defis->getEquipeDefiee())->getName() . ')'
        );
    }

    /**
     * @param Defis $defis
     * @return Infos
     */
    public function defisRealise(Defis $defis) : Infos
    {
        return $this->publierUnMessage(
            'Le défis ' . self::HREF . $this->urlPrefix . self::TEAM_URL
            . $defis->getEquipeOrigine()->getTeamId() . '">' . $defis->getEquipeOrigine()->getName() . '</a> contre '
            . self::HREF . $this->urlPrefix . self::TEAM_URL . $defis->getEquipeDefiee()->getTeamId() . '">'
            . $defis->getEquipeDefiee()->getName() . '</a> a été réalisé : ' . self::HREF . $this->urlPrefix
            . '/match/' . $defis->getMatchDefi()->getMatchId()  . '">Voir</a>'
        );
    }

    /**
     * @param Primes $prime
     * @return Infos
     */
    public function primeMise(Primes $prime) : Infos
    {
        return $this->publierUnMessage(
            $prime->getPlayers()->getName() . ', '
            . RulesetEnum::getPositionFromPlayerByRuleset($prime->getPlayers())->getPos()  . ' '
            . RulesetEnum::getRaceFromJoueurByRuleset($prime->getPlayers())->getName() . ' de '
            . $prime->getPlayers()->getOwnedByTeam()->getName() .
            ' a une prime de ' . $prime->getMontant() . ' Po'
        );
    }

    /**
     * @param Primes $prime
     * @return Infos
     */
    public function primeGagnee(Primes $prime) : infos
    {
        return $this->publierUnMessage(
            $prime->getEquipePrime()->getName() . ' a touché la prime de ' . $prime->getMontant() . 'Po sur '
            . $prime->getPlayers()->getName()  . ', '
            . RulesetEnum::getPositionFromPlayerByRuleset($prime->getPlayers())->getPos() . ' '
            . RulesetEnum::getRaceFromJoueurByRuleset($prime->getPlayers())->getName()
        );
    }

    public function sponsorAjoute(Teams $equipe): Infos
    {
        return $this->publierUnMessage(
            $equipe->getSponsor()->getName() . ' a décidé de sponsoriser '
            . '<a href="' . $this->urlPrefix . self::TEAM_URL . $equipe->getTeamId() . '">' . $equipe->getName() . '</a> !'
        );
    }

    public function sponsorSupprime(Teams $equipe): Infos
    {
        return $this->publierUnMessage(
            $equipe->getSponsor()->getName() . ' a décidé de ne plus sponsoriser '
            . '<a href="' . $this->urlPrefix . self::TEAM_URL . $equipe->getTeamId() . '">' . $equipe->getName() . '</a> !'
        );
    }

    //ajouter des messages pour les morts randomisés, et rajouter les cas

}
