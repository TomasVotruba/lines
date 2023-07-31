<?php

declare (strict_types=1);
namespace Lines202307;

use Lines202307\Illuminate\Container\Container;
use Lines202307\Symfony\Component\Console\Application;
use Lines202307\Symfony\Component\Console\Input\ArgvInput;
use Lines202307\Symfony\Component\Console\Input\ArrayInput;
use Lines202307\Symfony\Component\Console\Output\ConsoleOutput;
use Lines202307\Symfony\Component\Console\Style\SymfonyStyle;
use Lines202307\TomasVotruba\Lines\Console\Command\MeasureCommand;
use Lines202307\TomasVotruba\Lines\Console\Command\VendorCommand;
if (\file_exists(__DIR__ . '/../vendor/scoper-autoload.php')) {
    // A. build downgraded package
    require_once __DIR__ . '/../vendor/scoper-autoload.php';
} else {
    // B. local repository
    require_once __DIR__ . '/../vendor/autoload.php';
}
$container = new Container();
$container->singleton(SymfonyStyle::class, function () : SymfonyStyle {
    return new SymfonyStyle(new ArrayInput([]), new ConsoleOutput());
});
$application = new Application();
$measureCommand = $container->make(MeasureCommand::class);
$application->add($measureCommand);
$vendorCommand = $container->make(VendorCommand::class);
$application->add($vendorCommand);
$exitCode = $application->run(new ArgvInput(), new ConsoleOutput());
exit($exitCode);
