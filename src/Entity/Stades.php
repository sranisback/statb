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
     * @var GameDataStadium
     *
     * @ORM\ManyToOne(targetEntity="GameDataStadium")
     * @ORM\JoinColumn(name="f_type_stade_id", referencedColumnName="id", nullable=false)
     */
    private $fTypeStade;

    /**
     * @ORM\Column(type="integer")
     */
    private $TotalPayement = 0;

    /**
     * @ORM\Column(type="smallint")
     */
    private $niveau;

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

    public function getFTypeStade(): GameDataStadium
    {
        return $this->fTypeStade;
    }

    public function setFTypeStade(GameDataStadium $fTypeStade): self
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

    public function getNiveau(): ?int
    {
        return $this->niveau;
    }

    public function setNiveau(int $niveau): self
    {
        $this->niveau = $niveau;

        return $this;
    }
}
