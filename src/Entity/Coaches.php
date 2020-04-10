<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Security\Core\User\UserInterface;

/**
 * Coaches
 *
 * @ORM\Table(name="coaches")
 * @ORM\Entity(repositoryClass="App\Repository\CoachesRepository")
 */
class Coaches implements UserInterface
{
    /**
     *
     * @ORM\Column(name="coach_id", type="integer", nullable=false, options={"unsigned":true})
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     * @var int
     */
    private int $coachId;

    /**
     * @Assert\NotBlank
     *
     * @ORM\Column(name="name", type="string", length=60, nullable=true)
     * @var string|null
     */
    private ?string $name = null;

    /**
     *
     * @ORM\Column(name="passwd", type="string", length=64, nullable=true)
     * @var string|null
     */
    private ?string $passwd = null;

    /**
     *
     * @ORM\Column(name="role", type="json", nullable=false)
     * @var mixed[]
     */
    private array $roles;

    public function getCoachId(): int
    {
        return $this->coachId;
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

    public function getPasswd(): ?string
    {
        return $this->passwd;
    }

    public function setPasswd(string $passwd): self
    {
        $this->passwd = $passwd;

        return $this;
    }

    /**
     * @return mixed[]
     */
    public function getRoles(): array
    {
        return $this->roles;
    }

    public function getPassword(): ?string
    {
        return $this->passwd;
    }

    /**
     * @return null
     */
    public function getSalt()
    {
        return null;
    }

    public function getUsername(): ?string
    {
        return $this->name;
    }

    public function eraseCredentials(): void
    {
    }

    /** @see \Serializable::serialize() */
    public function serialize(): string
    {
        return serialize(array(
            $this->coachId,
            $this->name,
            $this->passwd,
        ));
    }

    /** @see \Serializable::unserialize()
     * @param string $serialized
     */
    public function unserialize(string $serialized): void
    {
        list (
            $this->coachId,
            $this->name,
            $this->passwd,
        ) = unserialize($serialized, ['allowed_classes' => false]);
    }

    /**
     * @param mixed[] $roles
     */
    public function setRoles(array $roles): void
    {
        $this->roles = $roles;
    }
}
