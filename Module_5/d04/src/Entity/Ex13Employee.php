<?php

namespace App\Entity;

use App\Repository\Ex13EmployeeRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;

#[ORM\Entity(repositoryClass: Ex13EmployeeRepository::class)]
#[ORM\Table(name: 'ex13_employee')]
#[UniqueEntity(fields: ['email'], message: 'Cet email est déjà utilisé')]
class Ex13Employee
{
   #[ORM\Id]
   #[ORM\GeneratedValue]
   #[ORM\Column(type: 'integer')]
   private ?int $id = null;

   #[ORM\Column(type: 'string', length: 255)]
   #[Assert\NotBlank(message: 'Le prénom est obligatoire')]
   #[Assert\Length(
       min: 2,
       max: 255,
       minMessage: 'Le prénom doit faire au moins {{ limit }} caractères',
       maxMessage: 'Le prénom ne peut pas faire plus de {{ limit }} caractères'
   )]
   private string $firstname;

   #[ORM\Column(type: 'string', length: 255)]
   #[Assert\NotBlank(message: 'Le nom est obligatoire')]
   #[Assert\Length(
       min: 2,
       max: 255,
       minMessage: 'Le nom doit faire au moins {{ limit }} caractères',
       maxMessage: 'Le nom ne peut pas faire plus de {{ limit }} caractères'
   )]
   private string $lastname;

   #[ORM\Column(type: 'string', length: 255, unique: true)]
   #[Assert\NotBlank(message: 'L\'email est obligatoire')]
   #[Assert\Email(message: 'L\'email {{ value }} n\'est pas un email valide')]
   private string $email;

   #[ORM\Column(type: Types::DATE_MUTABLE)]
   #[Assert\NotNull(message: 'La date de naissance est obligatoire')]
   #[Assert\LessThan(
       'today',
       message: 'La date de naissance doit être dans le passé'
   )]
   private \DateTimeInterface $birthdate;

   #[ORM\Column(type: 'boolean')]
   private bool $active = true;

   #[ORM\Column(type: Types::DATE_MUTABLE)]
   #[Assert\NotNull(message: 'La date d\'embauche est obligatoire')]
   private \DateTimeInterface $employedSince;

   #[ORM\Column(type: Types::DATE_MUTABLE, nullable: true)]
   #[Assert\GreaterThan(
       propertyPath: 'employedSince',
       message: 'La date de fin d\'emploi doit être postérieure à la date d\'embauche'
   )]
   private ?\DateTimeInterface $employedUntil = null;

   #[ORM\Column(type: "string", columnDefinition: "ENUM('8', '6', '4')")]
   #[Assert\Choice(
       choices: ['8', '6', '4'],
       message: 'Veuillez choisir un nombre d\'heures valide (8, 6 ou 4)'
   )]
   private string $hours = '8';

   #[ORM\Column(type: 'integer')]
   #[Assert\NotNull(message: 'Le salaire est obligatoire')]
   #[Assert\Positive(message: 'Le salaire doit être positif')]
   #[Assert\Range(
       min: 1000,
       max: 1000000,
       notInRangeMessage: 'Le salaire doit être entre {{ min }}€ et {{ max }}€'
   )]
   private int $salary;

   #[ORM\Column(type: "string", columnDefinition: "ENUM('manager', 'account_manager', 'qa_manager', 'dev_manager', 'ceo', 'coo', 'backend_dev', 'frontend_dev', 'qa_tester')")]
   #[Assert\NotBlank(message: 'Le poste est obligatoire')]
   #[Assert\Choice(choices: [
       'manager',
       'account_manager',
       'qa_manager',
       'dev_manager',
       'ceo',
       'coo',
       'backend_dev',
       'frontend_dev',
       'qa_tester'
   ], message: 'Veuillez choisir un poste valide')]
   private string $position;

   #[ORM\ManyToOne(targetEntity: self::class, inversedBy: 'subordinates')]
   #[ORM\JoinColumn(nullable: true)]
   private ?self $manager = null;

   #[ORM\OneToMany(targetEntity: self::class, mappedBy: 'manager')]
   private Collection $subordinates;

   public function __construct()
   {
       $this->subordinates = new ArrayCollection();
   }

   public function getId(): ?int
   {
       return $this->id;
   }

   public function getFirstname(): string
   {
       return $this->firstname;
   }

   public function setFirstname(string $firstname): self
   {
       $this->firstname = $firstname;
       return $this;
   }

   public function getLastname(): string
   {
       return $this->lastname;
   }

   public function setLastname(string $lastname): self
   {
       $this->lastname = $lastname;
       return $this;
   }

   public function getEmail(): string
   {
       return $this->email;
   }

   public function setEmail(string $email): self
   {
       $this->email = $email;
       return $this;
   }

   public function getBirthdate(): \DateTimeInterface
   {
       return $this->birthdate;
   }

   public function setBirthdate(\DateTimeInterface $birthdate): self
   {
       $this->birthdate = $birthdate;
       return $this;
   }

   public function isActive(): bool
   {
       return $this->active;
   }

   public function setActive(bool $active): self
   {
       $this->active = $active;
       return $this;
   }

   public function getEmployedSince(): \DateTimeInterface
   {
       return $this->employedSince;
   }

   public function setEmployedSince(\DateTimeInterface $employedSince): self
   {
       $this->employedSince = $employedSince;
       return $this;
   }

   public function getEmployedUntil(): ?\DateTimeInterface
   {
       return $this->employedUntil;
   }

   public function setEmployedUntil(?\DateTimeInterface $employedUntil): self
   {
       $this->employedUntil = $employedUntil;
       return $this;
   }

   public function getHours(): string
   {
       return $this->hours;
   }

   public function setHours(string $hours): self
   {
       $this->hours = $hours;
       return $this;
   }

   public function getSalary(): int
   {
       return $this->salary;
   }

   public function setSalary(int $salary): self
   {
       $this->salary = $salary;
       return $this;
   }

   public function getPosition(): string
   {
       return $this->position;
   }

   public function setPosition(string $position): self
   {
       $this->position = $position;
       return $this;
   }

   public function getManager(): ?self
   {
       return $this->manager;
   }

   public function setManager(?self $manager): self
   {
       $this->manager = $manager;
       return $this;
   }

   public function getSubordinates(): Collection
   {
       return $this->subordinates;
   }

   public function addSubordinate(self $subordinate): self
   {
       if (!$this->subordinates->contains($subordinate)) {
           $this->subordinates[] = $subordinate;
           $subordinate->setManager($this);
       }
       return $this;
   }

   public function removeSubordinate(self $subordinate): self
   {
       if ($this->subordinates->contains($subordinate)) {
           $this->subordinates->removeElement($subordinate);
           if ($subordinate->getManager() === $this) {
               $subordinate->setManager(null);
           }
       }
       return $this;
   }
}