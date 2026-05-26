<?php

declare (strict_types=1);
namespace Lines202605\TomasVotruba\Lines\DependencyInjection;

use Lines202605\Entropy\Container\Container;
use Lines202605\PhpParser\Parser;
use Lines202605\PhpParser\ParserFactory;
use Lines202605\Symfony\Component\Console\Application;
use Lines202605\Symfony\Component\Console\Command\Command;
use Lines202605\Symfony\Component\Console\Input\ArrayInput;
use Lines202605\Symfony\Component\Console\Output\ConsoleOutput;
use Lines202605\Symfony\Component\Console\Style\SymfonyStyle;
use Lines202605\TomasVotruba\Lines\Helpers\PrivatesAccessor;
final class ContainerFactory
{
    public function create() : Container
    {
        $this->emulateTokensOfOlderPHP();
        $container = new Container();
        $container->autodiscover(__DIR__ . '/../Command');
        // console
        $consoleVerbosity = \defined('PHPUNIT_COMPOSER_INSTALL') ? ConsoleOutput::VERBOSITY_QUIET : ConsoleOutput::VERBOSITY_NORMAL;
        $container->service(SymfonyStyle::class, static function () use($consoleVerbosity) : SymfonyStyle {
            return new SymfonyStyle(new ArrayInput([]), new ConsoleOutput($consoleVerbosity));
        });
        $container->service(Application::class, function (Container $container) : Application {
            $application = new Application();
            $commands = $container->findByContract(Command::class);
            $application->addCommands($commands);
            // remove basic command to make output clear
            $this->cleanupDefaultCommands($application);
            return $application;
        });
        $container->service(Parser::class, static function () : Parser {
            $phpParserFactory = new ParserFactory();
            return $phpParserFactory->createForHostVersion();
        });
        return $container;
    }
    private function cleanupDefaultCommands(Application $application) : void
    {
        $application->get('help')->setHidden(\true);
        PrivatesAccessor::propertyClosure($application, 'commands', static function (array $commands) : array {
            // remove default commands, as not needed here
            unset($commands['completion']);
            return $commands;
        });
    }
    private function emulateTokensOfOlderPHP() : void
    {
        // define fallback constants for PHP 8.0 tokens in case of e.g. PHP 7.2 run
        if (!\defined('T_MATCH')) {
            \define('T_MATCH', 5000);
        }
        if (!\defined('T_READONLY')) {
            \define('T_READONLY', 5010);
        }
        if (!\defined('T_ENUM')) {
            \define('T_ENUM', 5015);
        }
        if (!\defined('T_COALESCE_EQUAL')) {
            \define('T_COALESCE_EQUAL', 5020);
        }
        if (!\defined('T_FN')) {
            \define('T_FN', 5030);
        }
        if (!\defined('T_BAD_CHARACTER')) {
            \define('T_BAD_CHARACTER', 5040);
        }
    }
}
