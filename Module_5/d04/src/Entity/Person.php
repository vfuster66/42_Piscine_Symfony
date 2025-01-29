<?php

namespace App\Entity;

use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
#[ORM\Table(name: 'persons')]
class Person
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private int $id;

    #[ORM\Column(type: 'string', length: 255, unique: true)]
    private string $username;

    #[ORM\Column(type: 'string', length: 255)]
    private string $name;

    #[ORM\Column(type: 'string', length: 255, unique: true)]
    private string $email;

    #[ORM\Column(type: 'boolean')]
    private bool $enable;

    #[ORM\Column(type: 'datetime')]
    private \DateTimeInterface $birthdate;

    #[ORM\Column(type: Types::TEXT)]
    private string $address;

    // Getters et setters...
    public function getId(): int { return $this->id; }

    public function getUsername(): string { return $this->username; }
    public function setUsername(string $username): self { $this->username = $username; return $this; }

    public function getName(): string { return $this->name; }
    public function setName(string $name): self { $this->name = $name; return $this; }

    public function getEmail(): string { return $this->email; }
    public function setEmail(string $email): self { $this->email = $email; return $this; }

    public function isEnable(): bool { return $this->enable; }
    public function setEnable(bool $enable): self { $this->enable = $enable; return $this; }

    public function getBirthdate(): \DateTimeInterface { return $this->birthdate; }
    public function setBirthdate(\DateTimeInterface $birthdate): self { $this->birthdate = $birthdate; return $this; }

    public function getAddress(): string { return $this->address; }
    public function setAddress(string $address): self { $this->address = $address; return $this; }
}
