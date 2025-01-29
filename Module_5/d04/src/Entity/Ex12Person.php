<?php

namespace App\Entity;

use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use App\Repository\Ex12PersonRepository;

#[ORM\Entity(repositoryClass: Ex12PersonRepository::class)]
#[ORM\Table(name: 'ex12_person')]
class Ex12Person
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

    #[ORM\Column(type: "string", columnDefinition: "ENUM('single', 'married', 'widower')")]
    private string $maritalStatus = 'single';
    

    #[ORM\OneToOne(targetEntity: Ex12BankAccount::class, cascade: ['persist', 'remove'], mappedBy: 'person')]
    private ?Ex12BankAccount $bankAccount = null;

    #[ORM\OneToMany(targetEntity: Ex12Address::class, mappedBy: 'person', cascade: ['persist', 'remove'], orphanRemoval: true)]
    private Collection $addresses;

    public function __construct()
    {
        $this->addresses = new ArrayCollection();
    }

    // Getter et setter pour BankAccount
    public function getBankAccount(): ?Ex12BankAccount
    {
        return $this->bankAccount;
    }

    public function setBankAccount(?Ex12BankAccount $bankAccount): self
    {
        if ($bankAccount) {
            $bankAccount->setPerson($this);
        }
        $this->bankAccount = $bankAccount;

        return $this;
    }

    // Getters et setters pour Addresses
    public function getAddresses(): Collection
    {
        return $this->addresses;
    }

    public function addAddress(Ex12Address $address): self
    {
        if (!$this->addresses->contains($address)) {
            $this->addresses->add($address);
            $address->setPerson($this);
        }
        return $this;
    }

    public function removeAddress(Ex12Address $address): self
    {
        if ($this->addresses->removeElement($address)) {
            if ($address->getPerson() === $this) {
                $address->setPerson(null);
            }
        }
        return $this;
    }

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

    public function getMaritalStatus(): string
    {
        return $this->maritalStatus;
    }

    public function setMaritalStatus(string $maritalStatus): self
    {
        $this->maritalStatus = $maritalStatus;
        return $this;
    }

}
