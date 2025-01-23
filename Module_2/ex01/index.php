<?php
require_once 'TemplateEngine.php';
require_once 'Text.php';

$text = new Text(['Premier paragraphe', 'Second paragraphe']);
$text->append('Troisième paragraphe');

$engine = new TemplateEngine();
$engine->createFile('output.html', $text);