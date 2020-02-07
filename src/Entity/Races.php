<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Races
 *
 * @ORM\Table(name="races")
 * @ORM\Entity
 */
class Races
{
    /**
     *
     * @ORM\Column(name="race_id", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private int $raceId;

    /**
     *
     * @ORM\Column(name="name", type="string", length=60, nullable=true)
     */
    private ?string $name = null;

    /**
     *
     * @ORM\Column(name="cost_rr", type="integer", nullable=true, options={"unsigned":true})
     */
    private ?int $costRr;

    /**
     *
     * @ORM\Column(name="icon", type="string", length=45, nullable=true)
     */
    private ?string $icon;

    public function getRaceId(): ?int
    {
        return $this->raceId;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(?string $name): self
    {
        $this->name = $name;

        return $this;
    }

    public function getCostRr(): ?int
    {
        return $this->costRr;
    }

    public function setCostRr(?int $costRr): self
    {
        $this->costRr = $costRr;

        return $this;
    }

    public function getIcon(): ?string
    {
        return $this->icon;
    }

    public function setIcon(?string $icon): self
    {
        $this->icon = $icon;

        return $this;
    }
}
