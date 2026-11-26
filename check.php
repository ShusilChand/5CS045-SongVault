<?php
header('Content-Type: text/plain');

$path = dirname(__DIR__, 2) . '/song_config.php';

echo "PHP version: " . PHP_VERSION . "\n";
echo "Config readable: " . (is_readable($path) ? 'YES' : 'NO') . "\n";

if (function_exists('posix_geteuid')) {
    $user = posix_getpwuid(posix_geteuid());
    echo "PHP user: " . $user['name'] . "\n";
}