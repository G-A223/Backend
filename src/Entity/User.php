<?php

declare(strict_types=1);

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity]
#[ORM\Table(name: 'users')]
class User
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private $id;

    #[ORM\Column(type: 'string', length: 225)]
    #[Assert\NotBlank]
    #[Assert\Length(min: 3, max: 255)]
    private $name;

    #[ORM\Column(type: 'string', length: 11)]
    #[Assert\NotBlank]
    private $phone;


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

    public function __construct()
    {
        $this->reservations = new ArrayCollection();
    }
}
