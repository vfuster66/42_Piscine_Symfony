<?php
class TemplateEngine {
    public function createFile($fileName, $templateName, $parameters) {
        $template = file_get_contents($templateName);
        
        foreach ($parameters as $key => $value) {
            $template = str_replace('{'.$key.'}', $value, $template);
        }
        
        file_put_contents($fileName, $template);
    }
}