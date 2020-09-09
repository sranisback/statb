<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\GameDataStadiumRepository")
 */
class GameDataStadium
{
    /**
     * @ORM\Id
     * @var int|null
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private ?int $id = null;

    /**
     * @ORM\Column(type="string", length=50)
     * @var string
     */
    private string $famille;

    /**
     * @ORM\Column(type="string", length=50)
     * @var string
     */
    private string $type;

    /**
     * @ORM\Column(type="string", length=1500)
     * @var string
     */
    private string $effect;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getFamille(): string
    {
        return $this->famille;
    }

    public function setFamille(string $famille): self
    {
        $this->famille = $famille;

        return $this;
    }

    public function getType(): string
    {
        return $this->type;
    }

    public function setType(string $type): self
    {
        $this->type = $type;

        return $this;
    }

    public function getEffect(): string
    {
        return $this->effect;
    }

    public function setEffect(string $effect): self
    {
        $this->effect = $effect;

        return $this;
    }
}
