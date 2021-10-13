<?php

namespace App\Entity;

use App\Repository\GameDataSkillsBb2020Repository;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=GameDataSkillsBb2020Repository::class)
 */
class GameDataSkillsBb2020
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     * @var int
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=60)
     * @var string
     */
    private $name;

    /**
     * @ORM\Column(type="string", length=1)
     * @var string
     */
    private $cat;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): self
    {
        $this->name = $name;

        return $this;
    }

    public function getCat(): ?string
    {
        return $this->cat;
    }

    public function setCat(string $cat): self
    {
        $this->cat = $cat;

        return $this;
    }
}
