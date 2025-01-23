<?php
class TemplateEngine {
   public function createFile(HotBeverage $beverage) {
       $template = file_get_contents('template.html');
       $reflection = new ReflectionClass($beverage);
       
       $template = str_replace('{nom}', $beverage->getName(), $template);
       $template = str_replace('{price}', $beverage->getPrice(), $template);
       $template = str_replace('{resistance}', $beverage->getResistance(), $template);
       $template = str_replace('{description}', $beverage->getDescription(), $template);
       $template = str_replace('{comment}', $beverage->getComment(), $template);
       
       $className = strtolower($reflection->getShortName());
       file_put_contents($className . '.html', $template);
   }
}