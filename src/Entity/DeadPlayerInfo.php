<?php

namespace App\Entity;

use App\Repository\DeadPlayerInfoRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=DeadPlayerInfoRepository::class)
 */
class DeadPlayerInfo
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $phrase;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getPhrase(): ?string
    {
        return $this->phrase;
    }

    public function setPhrase(string $phrase): self
    {
        $this->phrase = $phrase;

        return $this;
    }
}
