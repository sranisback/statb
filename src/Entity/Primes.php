<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\PrimesRepository")
 */
class Primes
{
    /**
     * @ORM\Id
     * @var int|null
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private ?int $id = null;

    /**
     * @ORM\Column(type="integer")
     * @var int|null
     */
    private ?int $montant = 0;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Coaches")
     * @ORM\JoinColumn(name="coach_id", referencedColumnName="coach_id", nullable=false)
     * @var \App\Entity\Coaches|null
     */
    private ?\App\Entity\Coaches $Coaches = null;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Players")
     * @ORM\JoinColumn(name="player_id", referencedColumnName="player_id")
     * @var \App\Entity\Players|null
     */
    private ?\App\Entity\Players $players  = null;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Teams")
     * @ORM\JoinColumn(name="team_id", referencedColumnName="team_id")
     * @var \App\Entity\Teams|null
     */
    private ?\App\Entity\Teams $teams = null;

    /**
     * @ORM\Column(type="datetime")
     * @var \DateTimeInterface|null
     */
    private ?\DateTimeInterface $dateAjoutee = null;

    /**
     * @ORM\Column(type="integer")
     * @var int
     */
    private int $actif=1;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getMontant(): int
    {
        return $this->montant;
    }

    public function setMontant(int $montant): self
    {
        $this->montant = $montant;

        return $this;
    }

    public function getCoaches(): ?\App\Entity\Coaches
    {
        return $this->Coaches;
    }

    public function setCoaches(?Coaches $Coaches): self
    {
        $this->Coaches = $Coaches;

        return $this;
    }

    public function getPlayers(): ?Players
    {
        return $this->players;
    }

    public function setPlayers(?Players $players): self
    {
        $this->players = $players;

        return $this;
    }

    public function getDateAjoutee(): ?\DateTimeInterface
    {
        return $this->dateAjoutee;
    }

    public function setDateAjoutee(\DateTimeInterface $dateAjoutee): self
    {
        $this->dateAjoutee = $dateAjoutee;

        return $this;
    }

    public function getTeams(): ?\App\Entity\Teams
    {
        return $this->teams;
    }

    /**
     * @param Teams $teams
     * @return $this
     */
    public function setTeams(Teams $teams): self
    {
        $this->teams = $teams;

        return $this;
    }

    /**
     * @return int
     */
    public function getActif(): int
    {
        return $this->actif;
    }

    /**
     * @param integer $actif
     */
    public function setActif(int $actif): void
    {
        $this->actif = $actif;
    }
}
