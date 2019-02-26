<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Citations
 *
 * @ORM\Table(name="citations")
 * @ORM\Entity
 */
class Citations
{
    /**
     * @var int
     *
     * @ORM\Column(name="id_cit", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $idCit;

    /**
     * @var string
     *
     * @ORM\Column(name="citation", type="string", length=180, nullable=false)
     */
    private $citation;

    /**
     * @var string
     *
     * @ORM\Column(name="auteur", type="string", length=15, nullable=false)
     */
    private $auteur;

    public function getIdCit(): ?int
    {
        return $this->idCit;
    }

    public function getCitation(): ?string
    {
        return $this->citation;
    }

    public function setCitation(string $citation): self
    {
        $this->citation = $citation;

        return $this;
    }

    public function getAuteur(): ?string
    {
        return $this->auteur;
    }

    public function setAuteur(string $auteur): self
    {
        $this->auteur = $auteur;

        return $this;
    }
}
