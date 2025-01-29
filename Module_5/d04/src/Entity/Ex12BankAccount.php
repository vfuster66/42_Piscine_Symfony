<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use App\Entity\Ex12Person;

#[ORM\Entity]
#[ORM\Table(name: 'ex12_bank_account')]
class Ex12BankAccount
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private ?int $id = null;

    #[ORM\Column(type: 'string', length: 255, unique: true)]
    private string $accountNumber;

    #[ORM\Column(type: 'decimal', precision: 10, scale: 2)]
    private float $balance;

    #[ORM\OneToOne(inversedBy: 'bankAccount', targetEntity: Ex12Person::class)]
    #[ORM\JoinColumn(nullable: false, onDelete: 'CASCADE')]
    private ?Ex12Person $person = null;

    // Getters and Setters
    public function getId(): ?int
    {
        return $this->id;
    }

    public function getAccountNumber(): string
    {
        return $this->accountNumber;
    }

    public function setAccountNumber(string $accountNumber): self
    {
        $this->accountNumber = $accountNumber;
        return $this;
    }

    public function getBalance(): float
    {
        return $this->balance;
    }

    public function setBalance(float $balance): self
    {
        $this->balance = $balance;
        return $this;
    }

    public function getPerson(): ?Ex12Person
    {
        return $this->person;
    }

    public function setPerson(?Ex12Person $person): self
    {
        $this->person = $person;
        return $this;
    }
}
