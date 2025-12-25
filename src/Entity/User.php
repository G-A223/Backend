<?php

declare(strict_types=1);

namespace App\Entity;

use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\GetCollection;
use ApiPlatform\Metadata\Post;
use App\Controller\UserController;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity]
#[ORM\Table(name: 'users')]
#[ApiResource(
    operations: [
        new GetCollection(
            name: 'users',
            uriTemplate: '/api/users',
            controller: UserController::class . '::getUsers',
        ),
        new Post(
            name: 'create_user',
            uriTemplate: '/api/create_user',
            controller: UserController::class . '::createUser',
        )
    ]
)]
class User implements UserInterface, PasswordAuthenticatedUserInterface
{
    /**
     * @var null|integer
     */
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private $id;

    /**
     * @var null|string
     */
    #[ORM\Column(type: 'string', length: 225)]
    #[Assert\NotBlank]
    #[Assert\Length(min: 3, max: 255)]
    private $name;

    /**
     * @var null|string
     */
    #[ORM\Column(type: 'string', length: 11)]
    #[Assert\NotBlank]
    private $phone;

    #[ORM\Column(type: 'string')]
    #[Assert\NotBlank]
    #[Assert\Length(min: 8, max: 128)]
    private $password;

    #[ORM\Column(type: 'json')]
    private array $roles = [];

    #[ORM\OneToMany(targetEntity: Reservation::class, mappedBy: 'user')]
    private Collection $reservations;


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

    public function getPhone(): ?string
    {
        return $this->phone;
    }

    public function setPhone(string $phone): self
    {
        $this->phone = $phone;

        return $this;
    }

    public function getReservations(): Collection
    {
        return $this->reservations;
    }

    public function getUserIdentifier(): string
    {
        return (string) $this->phone;
    }

    public function getPassword(): ?string
    {
        return $this->password;
    }

    public function setPassword(string $password): self
    {
        $this->password = $password;

        return $this;
    }

    public function getRoles(): array
    {
        $roles = $this->roles;
        $roles[] = 'ROLE_USER';

        return array_unique($roles);
    }

    public function setRoles(array $roles): self
    {
        $this->roles = $roles;

        return $this;
    }

    public function eraseCredentials(): void
    {
    }

    public function __construct()
    {
        $this->reservations = new ArrayCollection();
    }
}
