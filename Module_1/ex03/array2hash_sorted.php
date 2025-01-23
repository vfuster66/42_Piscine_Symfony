<?php
function array2hash_sorted($array) {
    $hash = array();
    foreach ($array as $item) {
        $hash[$item[0]] = $item[1];
    }
    krsort($hash);
    return $hash;
}