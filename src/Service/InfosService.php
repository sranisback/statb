<?php


namespace App\Service;

use App\Entity\Infos;
use App\Entity\Players;
use App\Entity\Teams;
use Doctrine\ORM\EntityManagerInterface;
use Nette\Utils\DateTime;

class InfosService
{
    private $doctrineEntityManager;

    public function __construct(EntityManagerInterface $doctrineEntityManager)
    {
        $this->doctrineEntityManager = $doctrineEntityManager;
    }

    public function publierUnMessage($text)
    {
        $message = new Infos();
        $message->setMessages($text);
        $message->setDate(DateTime::createFromFormat("Y-m-d H:i:s", date("Y-m-d H:i:s")));

        $this->doctrineEntityManager->persist($message);
        $this->doctrineEntityManager->flush();

        return $message;
    }

    public function infosEquipeEstCree(Teams $equipe)
    {
        return $this->publierUnMessage(
            $equipe->getOwnedByCoach()->getName() .
            ' a crée l\'équipe <a href="/team/' . $equipe->getTeamId() .
            '">' . $equipe->getName() . '</a>' .
            '(' . $equipe->getFRace()->getName() .
            ')'
        );
    }

    public function infosJoueurEngage(Players $joueur)
    {
        return $this->publierUnMessage(
            $joueur->getName() . ', ' . $joueur->getFPos()->getPos() . ' ' .$joueur->getFRid()->getName() .
            ' a été engagé par <a href="/team/' . $joueur->getOwnedByTeam()->getTeamId() . '">' .
            $joueur->getOwnedByTeam()->getName() . '</a>' .
            ' de ' . $joueur->getOwnedByTeam()->getOwnedByCoach()->getName()
        );
    }
}
