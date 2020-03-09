<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\MeteoRepository")
 */
class Meteo
{
    /**
     * @ORM\Id
     * @var int
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private ?int $id = 0;

    /**
     * @ORM\Column(type="string", length=25)
     * @var string|null
     */
    private ?string $nom = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getNom(): ?string
    {
        return $this->nom;
    }

    public function setNom(string $nom): self
    {
        $this->nom = $nom;

        return $this;
    }
}
