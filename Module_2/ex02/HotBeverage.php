<?php
abstract class HotBeverage {
    protected string $name;
    protected float $price;
    protected int $resistance;

    public function getName(): string {
        return $this->name;
    }

    public function getPrice(): float {
        return $this->price;
    }

    public function getResistance(): int {
        return $this->resistance;
    }

    abstract public function getDescription(): string;
    abstract public function getComment(): string;
}