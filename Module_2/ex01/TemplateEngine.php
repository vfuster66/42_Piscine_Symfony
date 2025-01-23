<?php
class TemplateEngine {
    public function createFile($fileName, $text) {
        $html = "<!DOCTYPE html>\n<html>\n<body>\n";
        $html .= $text->readData();
        $html .= "</body>\n</html>";
        file_put_contents($fileName, $html);
    }
}