<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\PrimesRepository")
 */
class Primes
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="integer")
     */
    private $montant;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Coaches")
     * @ORM\JoinColumn(name="coach_id", referencedColumnName="coach_id", nullable=false)
     */
    private $Coaches;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Players")
     * @ORM\JoinColumn(name="player_id", referencedColumnName="player_id")
     */
    private $players;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Teams")
     * @ORM\JoinColumn(name="team_id", referencedColumnName="team_id")
     */
    private $teams;

    /**
     * @ORM\Column(type="datetime")
     */
    private $dateAjoutee;

    /**
     * @ORM\Column(type="integer")
     */
    private $actif=1;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getMontant(): ?int
    {
        return $this->montant;
    }

    public function setMontant(int $montant): self
    {
        $this->montant = $montant;

        return $this;
    }

    public function getCoaches(): ?Coaches
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

    public function getTeams()
    {
        return $this->teams;
    }

    public function setTeams($teams): self
    {
        $this->teams = $teams;

        return $this;
    }

    public function getActif()
    {
        return $this->actif;
    }

    public function setActif($actif): void
    {
        $this->actif = $actif;
    }
}
