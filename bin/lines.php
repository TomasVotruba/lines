<?php

declare (strict_types=1);
namespace Lines202509;

use Lines202509\Symfony\Component\Console\Application;
use Lines202509\Symfony\Component\Console\Input\ArgvInput;
use Lines202509\Symfony\Component\Console\Output\ConsoleOutput;
use Lines202509\TomasVotruba\Lines\DependencyInjection\ContainerFactory;
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
// Allow both "bin/lines src" and "bin/lines measure src"
$args = $_SERVER['argv'];
if (isset($args[1]) && \in_array($args[1], ['measure', 'm'], \true)) {
    \array_splice($args, 1, 1);
    // drop the explicit command name
}
$input = new ArgvInput($args);
$exitCode = $application->run($input, new ConsoleOutput());
exit($exitCode);
