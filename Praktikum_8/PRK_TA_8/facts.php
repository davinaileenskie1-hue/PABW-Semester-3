<?php

$lock = json_decode(file_get_contents('composer.lock'), true);
$want = ['nwidart/laravel-modules', 'wikimedia/composer-merge-plugin', 'laravel/framework', 'pestphp/pest'];
foreach ($lock['packages'] as $p) {
    if (in_array($p['name'], $want, true)) {
        echo $p['name'].' '.$p['version'].PHP_EOL;
    }
}
foreach ($lock['packages-dev'] as $p) {
    if (in_array($p['name'], $want, true)) {
        echo $p['name'].' '.$p['version'].PHP_EOL;
    }
}

$pdo = new PDO('mysql:host=127.0.0.1', 'root', '');
echo 'MySQL '.$pdo->query('select version()')->fetchColumn().PHP_EOL;

echo '--- modules_statuses ---'.PHP_EOL;
echo file_get_contents('modules_statuses.json');
