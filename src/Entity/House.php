<?php

declare(strict_types=1);

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity]
#[ORM\Table(name: 'houses')]
class House
{
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
    #[ORM\Column(type: 'text')]
    private $facilities;

    /**
     * @var int|null
     */
    #[ORM\Column(type: 'integer')]
    private $beds;

    /**
     * @var int|null
     */
    #[ORM\Column(type: 'integer')]
    private $bathrooms;

    /**
     * @var float|null
     */
    #[ORM\Column(type: 'float')]
    private $price;

    /**
     * @var int|null
     */
    #[ORM\Column(type: 'integer')]
    private $available;

    #[ORM\OneToMany(targetEntity: Reservation::class, mappedBy: 'house')]
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

    public function getBeds(): ?int
    {
        return $this->beds;
    }

    public function setBeds(int $beds): self
    {
        $this->beds = $beds;

        return $this;
    }

    public function getBathrooms(): ?int
    {
        return $this->bathrooms;
    }

    public function setBathrooms(int $bathrooms): self
    {
        $this->bathrooms = $bathrooms;

        return $this;
    }

    public function getPrice(): ?float
    {
        return $this->price;
    }

    public function setPrice(float $price): self
    {
        $this->price = $price;

        return $this;
    }

    public function getAvailable(): ?int
    {
        return $this->available;
    }

    public function setAvailable(int $available): self
    {
        $this->available = $available;

        return $this;
    }

    public function getFacilities(): ?string
    {
        return $this->facilities;
    }

    public function setFacilities(string $facilities)
    {
        $this->facilities = $facilities;

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
