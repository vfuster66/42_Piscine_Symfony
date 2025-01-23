<?php
require_once 'Elem.php';
require_once 'MyException.php';
require_once 'TemplateEngine.php';

try {
   // Test 1: Valid HTML with attributes
   $elem = new Elem('html');
   $body = new Elem('body');
   $body->pushElement(new Elem('p', 'Lorem ipsum', ['class' => 'text-muted']));
   $elem->pushElement($body);
   echo $elem->getHTML() . "\n";

   // Test 2: Invalid tag (should throw exception)
   $elem = new Elem('undefined');
} catch (MyException $e) {
   echo "MyException caught: " . $e->getMessage() . "\n";
}