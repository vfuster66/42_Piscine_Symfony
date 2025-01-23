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

function search_by_states($input) {
    global $states, $capitals;
    $search_terms = array_map('trim', explode(',', $input));
    
    foreach ($search_terms as $term) {
        if (isset($states[$term])) {
            echo $capitals[$states[$term]] . " is the capital of " . $term . ".\n";
        } elseif (in_array($term, $capitals)) {
            $state = array_search(array_search($term, $capitals), $states);
            echo $term . " is the capital of " . $state . ".\n";
        } else {
            echo $term . " is neither a capital nor a state.\n";
        }
    }
}