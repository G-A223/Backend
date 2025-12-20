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


    #[ORM\OneToMany(targetEntity: Reservation::class, mappedBy: 'user')]
    private Collection $reservations;


    public function setName(string $name): static
    {
        $this->name = $name;

        return $this;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setPhone(string $phone): static
    {
        $this->phone = $phone;

        return $this;
    }

    public function __construct()
    {
        $this->reservations = new ArrayCollection();
    }
}
