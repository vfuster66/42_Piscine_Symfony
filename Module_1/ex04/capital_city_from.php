<?php
$states = [
    'Oregon' => 'OR',
    'Alabama' => 'AL',
    'New Jersey' => 'NJ',
    'Colorado' => 'CO'
];

$capitals = [
    'OR' => 'Salem',
    'AL' => 'Montgomery',
    'NJ' => 'trenton',
    'KS' => 'Topeka'
];

function capital_city_from($state) {
    global $states, $capitals;
    if (isset($states[$state]) && isset($capitals[$states[$state]])) {
        return $capitals[$states[$state]] . "\n";
    }
    return "Unknown\n";
}