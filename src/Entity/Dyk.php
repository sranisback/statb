<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\DykRepository")
 */
class Dyk
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private int $id;

    /**
     * @ORM\Column(type="string", length=2500, nullable=true)
     * @var string|null
     */
    private ?string $dyk_text;

    /**
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    public function getDykText(): ?string
    {
        return $this->dyk_text;
    }

    public function setDykText(?string $dyk_text): self
    {
        $this->dyk_text = $dyk_text;

        return $this;
    }
}
