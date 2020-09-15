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
     * @ORM\ManyToOne(targetEntity="App\Entity\Players")
     * @ORM\JoinColumn(name="player_id", referencedColumnName="player_id")
     * @var \App\Entity\Players|null
     */
    private ?\App\Entity\Players $players  = null;

    /**
     * @ORM\OneToOne(targetEntity="App\Entity\Teams")
     * @ORM\JoinColumn(name="equipe_prime", referencedColumnName="team_id", nullable=true)
     * @var \App\Entity\Teams|null
     */
    private ?\App\Entity\Teams $equipePrime  = null;

    /**
     * @return Teams|null
     */
    public function getEquipePrime(): ?Teams
    {
        return $this->equipePrime;
    }

    /**
     * @param Teams|null $equipePrime
     */
    public function setEquipePrime(?Teams $equipePrime): void
    {
        $this->equipePrime = $equipePrime;
    }

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

    public function getPlayers(): ?Players
    {
        return $this->players;
    }

    public function setPlayers(?Players $players): self
    {
        $this->players = $players;

        return $this;
    }
}
