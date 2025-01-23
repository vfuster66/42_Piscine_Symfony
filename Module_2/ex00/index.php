<?php
require_once 'TemplateEngine.php';

$engine = new TemplateEngine();
$params = [
    'nom' => 'Mon Livre',
    'auteur' => 'John Doe',
    'description' => 'Un super livre',
    'prix' => '19.99'
];

$engine->createFile('output.html', 'book_description.html', $params);