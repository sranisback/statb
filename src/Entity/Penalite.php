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
    private $id;

    /**
     * @ORM\Column(type="integer")
     */
    private $points;

    /**
     * @ORM\ManyToOne(targetEntity=Teams::class, inversedBy="penalite")
     * @ORM\JoinColumn(referencedColumnName="team_id", nullable=false)
     */
    private $equipe;

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
}
