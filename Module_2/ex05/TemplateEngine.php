<?php
class TemplateEngine {
    public function createFile($fileName, Elem $element) {
        file_put_contents($fileName, $element->getHTML());
    }
 }