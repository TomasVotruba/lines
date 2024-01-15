<?php

declare (strict_types=1);
namespace Lines202401;

use Lines202401\Illuminate\Container\Container;
use Lines202401\Symfony\Component\Console\Application;
use Lines202401\Symfony\Component\Console\Input\ArgvInput;
use Lines202401\Symfony\Component\Console\Input\ArrayInput;
use Lines202401\Symfony\Component\Console\Output\ConsoleOutput;
use Lines202401\Symfony\Component\Console\Style\SymfonyStyle;
use Lines202401\TomasVotruba\Lines\Console\Command\MeasureCommand;
use Lines202401\TomasVotruba\Lines\Console\Command\VendorCommand;
use Lines202401\TomasVotruba\Lines\DependencyInjection\ContainerFactory;
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
$application = $container->make(Application::class);
$exitCode = $application->run(new ArgvInput(), new ConsoleOutput());
exit($exitCode);
