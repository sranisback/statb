<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\StadesRepository")
 */
class Stades
{
    /**
     * @ORM\Id
     * @var int|null
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private ?int $id = null;

    /**
     * @ORM\Column(type="string", length=50, nullable=true)
     * @var string|null
     */
    private ?string $nom = null;

    /**
     *
     * @ORM\ManyToOne(targetEntity="GameDataStadium")
     * @ORM\JoinColumn(name="f_type_stade_id", referencedColumnName="id", nullable=false)
     * @var \App\Entity\GameDataStadium|null
     */
    private ?\App\Entity\GameDataStadium $fTypeStade = null;

    /**
     * @ORM\Column(type="integer")
     * @var int
     */
    private int $TotalPayement = 0;

    /**
     * @ORM\Column(type="smallint")
     * @var int
     */
    private int $niveau = 0;

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

    public function getFTypeStade(): ?GameDataStadium
    {
        return $this->fTypeStade;
    }

    public function setFTypeStade(GameDataStadium $fTypeStade): self
    {
        $this->fTypeStade = $fTypeStade;

        return $this;
    }

    public function getTotalPayement(): int
    {
        return $this->TotalPayement;
    }

    public function setTotalPayement(int $TotalPayement): self
    {
        $this->TotalPayement = $TotalPayement;

        return $this;
    }

    public function getNiveau(): int
    {
        return $this->niveau;
    }

    public function setNiveau(int $niveau): self
    {
        $this->niveau = $niveau;

        return $this;
    }
}
