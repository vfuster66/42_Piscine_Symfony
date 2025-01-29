<?php

namespace App\Service;

class Ex03Service
{
    public function uppercaseWords(string $string): string
    {
        return ucwords(strtolower($string));
    }

    public function countNumbers(string $string): int
    {
        return preg_match_all('/[0-9]/', $string);
    }
}