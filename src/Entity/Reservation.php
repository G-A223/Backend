<?php

declare(strict_types=1);

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
#[ORM\Table(name: 'reservations')]
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


    public function setHouse(?House $house): static
    {
        $this->house = $house;

        return $this;
    }

    public function setUser(?User $user): static
    {
        $this->user = $user;

        return $this;
    }

    public function setComment(string $comment): static
    {
        $this->comment = $comment;

        return $this;
    }
}
