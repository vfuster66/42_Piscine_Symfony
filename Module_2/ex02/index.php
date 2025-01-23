<?php
require_once 'HotBeverage.php';
require_once 'Coffee.php';
require_once 'Tea.php';
require_once 'TemplateEngine.php';

$engine = new TemplateEngine();
$engine->createFile(new Coffee());
$engine->createFile(new Tea());