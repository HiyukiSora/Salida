<?php
header('Content-Type: text/plain');
echo "Commit: " . shell_exec('git log --oneline -1 2>/dev/null') . "\n";

echo "\n--- api/index.php ---\n";
echo file_get_contents(__DIR__ . '/index.php');

echo "\n\n--- includes/header.php ---\n";
echo file_get_contents(__DIR__ . '/../includes/header.php');
