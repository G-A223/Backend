<?php

declare(strict_types=1);

namespace App\Entity;

use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\GetCollection;
use ApiPlatform\Metadata\Post;
use ApiPlatform\Metadata\Put;
use App\Controller\ReserveController;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
#[ORM\Table(name: 'reservations')]
#[ApiResource(
    operations: [
        new GetCollection(
            name: 'reservations',
            uriTemplate: '/api/reservations',
            controller: ReserveController::class . '::getReservations',
        ),
        new Post(
            name: 'reserve',
            uriTemplate: '/api/reserve',
            controller: ReserveController::class . '::reserve',
        ),
        new Put(
            name: 'edit',
            uriTemplate: '/api/reserve/{id}',
            controller: ReserveController::class . '::edit',
        ),
    ]
)]
class Reservation
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private $id;

    #[ORM\ManyToOne(targetEntity: House::class, inversedBy: 'reservations')]
    private House $house;

    #[ORM\ManyToOne(targetEntity: User::class, inversedBy: 'reservations')]
    private User $user;

    #[ORM\Column(type: 'text')]
    private string $comment;

    public function __toString(): string
    {
        return sprintf(
            'Бронирование #%s (Дом: %s, Пользователь: %s)',
            $this->getId(),
            $this->getHouse() ? $this->getHouse()->getId() : 'N/A',
            $this->getUser() ? $this->getUser()->getId() : 'N/A'
        );
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getHouse(): ?House
    {
        return $this->house;
    }

    public function setHouse(?House $house): self
    {
        $this->house = $house;

        return $this;
    }

    public function getUser(): ?User
    {
        return $this->user;
    }

    public function setUser(?User $user): self
    {
        $this->user = $user;

        return $this;
    }

    public function getComment(): ?string
    {
        return $this->comment;
    }

    public function setComment(string $comment)
    {
        $this->comment = $comment;

        return $this;
    }
}
