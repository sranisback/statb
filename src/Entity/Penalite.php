<?php

namespace App\Entity;

use App\Repository\PenaliteRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=PenaliteRepository::class)
 */
class Penalite
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private int $id;

    /**
     * @ORM\Column(type="integer")
     */
    private int $points = 0;

    /**
     * @ORM\ManyToOne(targetEntity=Teams::class, inversedBy="penalite")
     * @ORM\JoinColumn(referencedColumnName="team_id", nullable=false)
     */
    private \App\Entity\Teams $equipe;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private ?string $motif = null;

    /**
     * @ORM\Column(type="date")
     */
    private \DateTimeInterface $date;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getPoints(): ?int
    {
        return $this->points;
    }

    public function setPoints(int $points): self
    {
        $this->points = $points;

        return $this;
    }

    public function getEquipe(): ?Teams
    {
        return $this->equipe;
    }

    public function setEquipe(?Teams $equipe): self
    {
        $this->equipe = $equipe;

        return $this;
    }

    public function getMotif(): ?string
    {
        return $this->motif;
    }

    public function setMotif(?string $motif): self
    {
        $this->motif = $motif;

        return $this;
    }

    public function getDate(): ?\DateTimeInterface
    {
        return $this->date;
    }

    public function setDate(\DateTimeInterface $date): self
    {
        $this->date = $date;

        return $this;
    }
}
