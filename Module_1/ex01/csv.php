<?php
// Vérifie si le fichier ex01.txt existe dans le même répertoire
if (!file_exists('ex01.txt')) {
    echo "Le fichier ex01.txt est introuvable.\n";
    exit(1);
}

// Lit le contenu du fichier
$content = file_get_contents('ex01.txt');

// Divise le contenu en éléments séparés par une virgule
$values = explode(',', $content);

// Affiche chaque valeur sur une nouvelle ligne
foreach ($values as $value) {
    echo trim($value) . "\n";
}
