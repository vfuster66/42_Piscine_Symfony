<?php
class Tea extends HotBeverage {
    private string $description;
    private string $comment;
 
    public function __construct() {
        $this->name = "Tea";
        $this->price = 1.5;
        $this->resistance = 3;
        $this->description = "A hot beverage made from tea leaves";
        $this->comment = "Best served with lemon";
    }
 
    public function getDescription(): string {
        return $this->description;
    }
 
    public function getComment(): string {
        return $this->comment;
    }
 }