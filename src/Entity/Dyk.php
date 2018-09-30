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
    private $id;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $dyk_text;

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
