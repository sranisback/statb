<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * GameDataSkills
 *
 * @ORM\Table(name="game_data_skills")
 * @ORM\Entity
 */
class GameDataSkills
{
    /**
     *
     * @ORM\Column(name="skill_id", type="smallint", nullable=false, options={"unsigned":true})
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     * @var int
     */
    private int $skillId;

    /**
     *
     * @ORM\Column(name="name", type="string", length=60, nullable=true)
     * @var string|null
     */
    private ?string $name;

    /**
     *
     * @ORM\Column(name="cat", type="string", length=1, nullable=true)
     * @var null|string
     */
    private ?string $cat = null;

    public function getSkillId(): int
    {
        return $this->skillId;
    }

    /***
     * @method getName
     * @return null|string
     */
    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(?string $name): self
    {
        $this->name = $name;

        return $this;
    }

    public function getCat(): ?string
    {
        return $this->cat;
    }

    public function setCat(?string $cat): self
    {
        $this->cat = $cat;

        return $this;
    }
}
