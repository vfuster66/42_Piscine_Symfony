<?php
include('./array2hash.php');

// Test basique
$array = array(array("Pierre", "30"), array("Mary", "28"));
$result = array2hash($array);
echo "Test 1 - Conversion basique:\n";
print_r($result);

// Test avec tableau vide
$empty = array();
$result2 = array2hash($empty);
echo "\nTest 2 - Tableau vide:\n";
print_r($result2);

// Test avec un seul élément
$single = array(array("Jean", "25"));
$result3 = array2hash($single);
echo "\nTest 3 - Un seul élément:\n";
print_r($result3);