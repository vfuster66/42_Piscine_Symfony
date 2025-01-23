<?php
include('./array2hash_sorted.php');

// Test principal
$array = array(array("Pierre","30"), array("Mary","28"), array("Nelly", "22"));
print_r(array2hash_sorted($array));

// Test tableau vide
$empty = array();
echo "\nTest tableau vide:\n";
print_r(array2hash_sorted($empty));

// Test un élément
$single = array(array("Jean", "25"));
echo "\nTest un élément:\n";
print_r(array2hash_sorted($single));