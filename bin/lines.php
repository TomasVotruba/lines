<?php

declare(strict_types=1);

if (!extension_loaded('intl')) {
    echo "The intl extension is required but not loaded.\n";
    exit(1);
}

use Symfony\Component\Console\Application;
use TomasVotruba\Lines\DependencyInjection\ContainerFactory;

if (file_exists(__DIR__ . '/../vendor/scoper-autoload.php')) {
    // A. build downgraded package
    require_once __DIR__ . '/../vendor/scoper-autoload.php';
} elseif (file_exists(__DIR__ . '/../../../../vendor/autoload.php')) {
    // B. dev repository
    require_once __DIR__ . '/../../../../vendor/autoload.php';
} else {
    // C. local repository
    require_once __DIR__ . '/../vendor/autoload.php';
}

$containerFactory = new ContainerFactory();
$container = $containerFactory->create();

$application = $container->make(Application::class);
exit($application->run());
