<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
#[ORM\Table(name: 'ex10_data')]
class Ex10Data
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private ?int $id = null;

    #[ORM\Column(type: 'string', length: 255)]
    private string $field1;

    #[ORM\Column(type: 'string', length: 255)]
    private string $field2;

    #[ORM\Column(type: 'datetime')]
    private \DateTimeInterface $field3;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getField1(): string
    {
        return $this->field1;
    }

    public function setField1(string $field1): self
    {
        $this->field1 = $field1;
        return $this;
    }

    public function getField2(): string
    {
        return $this->field2;
    }

    public function setField2(string $field2): self
    {
        $this->field2 = $field2;
        return $this;
    }

    public function getField3(): \DateTimeInterface
    {
        return $this->field3;
    }

    public function setField3(\DateTimeInterface $field3): self
    {
        $this->field3 = $field3;
        return $this;
    }
}
