<?php
class Coffee extends HotBeverage {
   private string $description;
   private string $comment;

   public function __construct() {
       $this->name = "Coffee";
       $this->price = 2.0;
       $this->resistance = 2;
       $this->description = "A hot beverage made from coffee beans";
       $this->comment = "Best served black";
   }

   public function getDescription(): string {
       return $this->description;
   }

   public function getComment(): string {
       return $this->comment;
   }
}