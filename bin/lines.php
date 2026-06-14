<?php

declare (strict_types=1);
namespace Lines202606;

use Lines202606\Entropy\Console\ConsoleApplication;
use Lines202606\TomasVotruba\Lines\DependencyInjection\ContainerFactory;
if (\file_exists(__DIR__ . '/../vendor/scoper-autoload.php')) {
    // A. build downgraded package
    require_once __DIR__ . '/../vendor/scoper-autoload.php';
} elseif (\file_exists(__DIR__ . '/../../../../vendor/autoload.php')) {
    // B. dev repository
    require_once __DIR__ . '/../../../../vendor/autoload.php';
} else {
    // C. local repository
    require_once __DIR__ . '/../vendor/autoload.php';
}
$containerFactory = new ContainerFactory();
$container = $containerFactory->create();
$consoleApplication = $container->make(ConsoleApplication::class);
exit($consoleApplication->run($argv));
