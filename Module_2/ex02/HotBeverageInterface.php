<?php
interface HotBeverageInterface {
    public function getName(): string;
    public function getPrice(): float; 
    public function getResistance(): int;
    public function getDescription(): string;
    public function getComment(): string;
}