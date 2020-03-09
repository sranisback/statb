<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Citations
 *
 * @ORM\Table(name="citations")
 * @ORM\Entity(repositoryClass="App\Repository\CitationsRepository")
 */
class Citations
{
    /**
     *
     * @ORM\Column(name="id_cit", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     * @var int
     */
    private int $idCit;

    /**
     *
     * @ORM\Column(name="citation", type="string", length=180, nullable=false)
     * @var string|null
     */
    private ?string $citation = null;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Coaches")
     *  @ORM\JoinColumn (name="coach_id", referencedColumnName="coach_id")
     * @var null|\App\Entity\Coaches
     */
    private ?\App\Entity\Coaches $coachId = null;


    public function getIdCit(): int
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

    public function getCoachId(): ?Coaches
    {
        return $this->coachId;
    }

    public function setCoachId(?Coaches $coachId): self
    {
        $this->coachId = $coachId;

        return $this;
    }
}
