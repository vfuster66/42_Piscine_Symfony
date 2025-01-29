<?php
// src/Entity/Admin.php

namespace App\Entity;

use App\Repository\AdminRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: AdminRepository::class)]
class Admin extends User
{
    #[ORM\Column(length: 255, nullable: true)]
    private ?string $adminCode = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    private ?\DateTimeInterface $lastLoginAt = null;

    public function __construct()
    {
        parent::__construct();
        $this->setRoles(['ROLE_ADMIN']);
        $this->lastLoginAt = new \DateTime();
    }

    public function getAdminCode(): ?string
    {
        return $this->adminCode;
    }

    public function setAdminCode(?string $adminCode): self
    {
        $this->adminCode = $adminCode;
        return $this;
    }

    public function getLastLoginAt(): ?\DateTimeInterface
    {
        return $this->lastLoginAt;
    }

    public function setLastLoginAt(\DateTimeInterface $lastLoginAt): self
    {
        $this->lastLoginAt = $lastLoginAt;
        return $this;
    }
}