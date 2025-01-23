<?php
function array2hash($array) {
    $hash = array();
    foreach ($array as $item) {
        $hash[$item[1]] = $item[0];
    }
    return $hash;
}
