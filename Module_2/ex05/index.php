<?php
require_once 'Elem.php';
require_once 'MyException.php';

// Test valide
$html = new Elem('html');
$head = new Elem('head');
$head->pushElement(new Elem('title', 'Test'));
$head->pushElement(new Elem('meta', null, ['charset' => 'utf-8']));
$html->pushElement($head);

$body = new Elem('body');
$html->pushElement($body);

echo "Valid HTML test: " . ($html->validPage() ? "true" : "false") . "\n";

// Test invalide (p avec élément enfant)
$html2 = new Elem('html');
$head2 = new Elem('head');
$head2->pushElement(new Elem('title', 'Test'));
$head2->pushElement(new Elem('meta', null, ['charset' => 'utf-8']));
$html2->pushElement($head2);

$body2 = new Elem('body');
$p = new Elem('p');
$p->pushElement(new Elem('span', 'Invalid'));
$body2->pushElement($p);
$html2->pushElement($body2);

echo "Invalid HTML test (p with child): " . ($html2->validPage() ? "true" : "false") . "\n";