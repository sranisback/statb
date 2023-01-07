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
    private int $id;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private int $gagne = 0;

    /**
     * @ORM\Column(type="integer")
     */
    private int $egalite = 0;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private int $perdu = 0;

    /**
     * @ORM\Column(type="float", nullable=true)
     */
    private float $points = 0;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private int $bonus = 0;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private int $tdPour = 0;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private int $tdContre = 0;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private int $casPour = 0;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private int $casContre = 0;

    /**
     * @ORM\OneToOne(targetEntity=Teams::class, cascade={"persist", "remove"})
     * @ORM\JoinColumn( referencedColumnName="team_id", nullable=true)
     */
    private ?Teams $equipe = null;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private int $penalite = 0;

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

    public function getPoints(): float
    {
        return $this->points;
    }

    public function setPoints(float $points): self
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
