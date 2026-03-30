<?php
$file = __DIR__ . '/src/Application/Controller/OffreController.php';
$content = file_get_contents($file);
$content = preg_replace('/\s*\/\/ DEBUG temporaire.*?die\([^)]+\);\s*/s', "\n\n", $content);
file_put_contents($file, $content);
echo str_contains($content, 'die(') ? "ERREUR\n" : "OK\n";
