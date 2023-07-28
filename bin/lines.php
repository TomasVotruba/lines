<?php

declare(strict_types=1);

use Illuminate\Container\Container;
use Symfony\Component\Console\Application;
use Symfony\Component\Console\Input\ArgvInput;
use Symfony\Component\Console\Output\ConsoleOutput;
use TomasVotruba\Lines\Console\Command\MeasureCommand;
use TomasVotruba\Lines\Console\Command\VendorCommand;

if (file_exists(__DIR__ . '/../vendor/scoper-autoload.php')) {
    // A. build downgraded package
    require_once __DIR__ . '/../vendor/scoper-autoload.php';
} else {
    // B. local repository
    require_once __DIR__ . '/../vendor/autoload.php';
}

$container = new Container();
$application = new Application();

$measureCommand = $container->make(MeasureCommand::class);
$application->add($measureCommand);

$vendorCommand = $container->make(VendorCommand::class);
$application->add($vendorCommand);

$exitCode = $application->run(new ArgvInput(), new ConsoleOutput());
exit($exitCode);
