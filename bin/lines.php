<?php

use TomasVotruba\Lines\CLI\Application;

$loaded = false;

foreach (array(__DIR__ . '/../../../autoload.php', __DIR__ . '/../vendor/autoload.php') as $filePath) {
    if (file_exists($filePath)) {
        require $filePath;
        $loaded = true;
        break;
    }
}

if (!$loaded) {
    die(
        'You need to set up the project dependencies using the following commands:' . PHP_EOL .
        'wget http://getcomposer.org/composer.phar' . PHP_EOL .
        'php composer.phar install' . PHP_EOL
    );
}

$application = new Application();
$statusCode = $application->run($_SERVER['argv']);

exit($statusCode);
