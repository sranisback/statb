<?php

namespace App\Entity;

use App\Repository\ClassementGeneralRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=ClassementGeneralRepository::class)
 */
class ClassementGeneral
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private $gagne;

    /**
     * @ORM\Column(type="integer")
     */
    private $egalite;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private $perdu;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private $points;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private $bonus;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private $tdPour;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private $tdContre;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private $casPour;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private $casContre;

    /**
     * @ORM\OneToOne(targetEntity=Teams::class, cascade={"persist", "remove"})
     * @ORM\JoinColumn( referencedColumnName="team_id", nullable=true)
     */
    private $equipe;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private $penalite;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getGagne(): ?int
    {
        return $this->gagne;
    }

    public function setGagne(?int $gagne): self
    {
        $this->gagne = $gagne;

        return $this;
    }

    public function getEgalite(): ?int
    {
        return $this->egalite;
    }

    public function setEgalite(int $egalite): self
    {
        $this->egalite = $egalite;

        return $this;
    }

    public function getPerdu(): ?int
    {
        return $this->perdu;
    }

    public function setPerdu(?int $perdu): self
    {
        $this->perdu = $perdu;

        return $this;
    }

    public function getPoints(): ?int
    {
        return $this->points;
    }

    public function setPoints(?int $points): self
    {
        $this->points = $points;

        return $this;
    }

    public function getBonus(): ?int
    {
        return $this->bonus;
    }

    public function setBonus(?int $bonus): self
    {
        $this->bonus = $bonus;

        return $this;
    }

    public function getTdPour(): ?int
    {
        return $this->tdPour;
    }

    public function setTdPour(?int $tdPour): self
    {
        $this->tdPour = $tdPour;

        return $this;
    }

    public function getTdContre(): ?int
    {
        return $this->tdContre;
    }

    public function setTdContre(?int $tdContre): self
    {
        $this->tdContre = $tdContre;

        return $this;
    }

    public function getCasPour(): ?int
    {
        return $this->casPour;
    }

    public function setCasPour(?int $casPour): self
    {
        $this->casPour = $casPour;

        return $this;
    }

    public function getCasContre(): ?int
    {
        return $this->casContre;
    }

    public function setCasContre(?int $casContre): self
    {
        $this->casContre = $casContre;

        return $this;
    }

    public function getEquipe(): ?Teams
    {
        return $this->equipe;
    }

    public function setEquipe(Teams $equipe): self
    {
        $this->equipe = $equipe;

        return $this;
    }

    public function getPenalite(): ?int
    {
        return $this->penalite;
    }

    public function setPenalite(?int $penalite): self
    {
        $this->penalite = $penalite;

        return $this;
    }
}
