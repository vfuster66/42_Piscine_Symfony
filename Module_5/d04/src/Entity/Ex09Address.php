<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use App\Entity\Ex09Person;

#[ORM\Entity]
#[ORM\Table(name: 'ex09_address')]
class Ex09Address
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private ?int $id = null;

    #[ORM\Column(type: 'string', length: 255)]
    private string $addressLine;

    #[ORM\Column(type: 'string', length: 255)]
    private string $city;

    #[ORM\Column(type: 'string', length: 20)]
    private string $postalCode;

    #[ORM\Column(type: 'string', length: 255)]
    private string $country;

    #[ORM\ManyToOne(targetEntity: Ex09Person::class, inversedBy: 'addresses')]
    #[ORM\JoinColumn(nullable: false, onDelete: 'CASCADE')]
    private ?Ex09Person $person = null;

    // Getters and Setters
    public function getId(): ?int
    {
        return $this->id;
    }

    public function getAddressLine(): string
    {
        return $this->addressLine;
    }

    public function setAddressLine(string $addressLine): self
    {
        $this->addressLine = $addressLine;
        return $this;
    }

    public function getCity(): string
    {
        return $this->city;
    }

    public function setCity(string $city): self
    {
        $this->city = $city;
        return $this;
    }

    public function getPostalCode(): string
    {
        return $this->postalCode;
    }

    public function setPostalCode(string $postalCode): self
    {
        $this->postalCode = $postalCode;
        return $this;
    }

    public function getCountry(): string
    {
        return $this->country;
    }

    public function setCountry(string $country): self
    {
        $this->country = $country;
        return $this;
    }

    public function getPerson(): ?Ex09Person
    {
        return $this->person;
    }

    public function setPerson(?Ex09Person $person): self
    {
        $this->person = $person;
        return $this;
    }
}
