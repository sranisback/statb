<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\StadesRepository")
 */
class Stades
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=50, nullable=true)
     */
    private $nom;

    /**
     * @ORM\ManyToOne(targetEntity="GameDataStades", fetch="EAGER")
     * @ORM\JoinColumn(nullable=false)
     */
    private $fTypeStade;

    /**
     * @ORM\Column(type="integer")
     */
    private $TotalPayement = 0;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getNom(): ?string
    {
        return $this->nom;
    }

    public function setNom(?string $nom): self
    {
        $this->nom = $nom;

        return $this;
    }

    public function getFTypeStade(): ?GameDataStades
    {
        return $this->fTypeStade;
    }

    public function setFTypeStade(?GameDataStades $fTypeStade): self
    {
        $this->fTypeStade = $fTypeStade;

        return $this;
    }

    public function getTotalPayement(): ?int
    {
        return $this->TotalPayement;
    }

    public function setTotalPayement(int $TotalPayement): self
    {
        $this->TotalPayement = $TotalPayement;

        return $this;
    }
}
