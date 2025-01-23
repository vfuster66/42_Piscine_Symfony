<?php
class Text {
    private array $strings = [];
    
    public function __construct(array $strings) {
        $this->strings = $strings;
    }
    
    public function append($text) {
        $this->strings[] = $text;
    }
    
    public function readData() {
        return implode("\n", array_map(function($str) {
            return "<p>$str</p>";
        }, $this->strings));
    }
}