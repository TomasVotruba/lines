<?php

declare(strict_types=1);

namespace TomasVotruba\Lines\DependencyInjection;

use Illuminate\Container\Container;
use PhpParser\Parser;
use PhpParser\ParserFactory;
use Symfony\Component\Console\Application;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Output\ConsoleOutput;
use Symfony\Component\Console\Style\SymfonyStyle;
use TomasVotruba\Lines\Command\CountFeaturesCommand;
use TomasVotruba\Lines\Command\MeasureCommand;
use TomasVotruba\Lines\Helpers\PrivatesAccessor;

final class ContainerFactory
{
    /**
     * @api used in bin and tests
     */
    public function create(): Container
    {
        $this->emulateTokensOfOlderPHP();

        $container = new Container();

        // console
        $consoleVerbosity = defined(
            'PHPUNIT_COMPOSER_INSTALL'
        ) ? ConsoleOutput::VERBOSITY_QUIET : ConsoleOutput::VERBOSITY_NORMAL;

        $container->singleton(
            SymfonyStyle::class,
            static fn (): SymfonyStyle => new SymfonyStyle(new ArrayInput([]), new ConsoleOutput($consoleVerbosity))
        );

        $container->singleton(Application::class, function (Container $container): Application {
            $application = new Application();

            $measureCommand = $container->make(MeasureCommand::class);
            $application->add($measureCommand);

            $countFeaturesCommand = $container->make(CountFeaturesCommand::class);
            $application->add($countFeaturesCommand);

            // remove basic command to make output clear
            $this->cleanupDefaultCommands($application);

            return $application;
        });

        // parser
        $container->singleton(Parser::class, static function (): Parser {
            $phpParserFactory = new ParserFactory();
            return $phpParserFactory->createForHostVersion();
        });

        return $container;
    }

    public function cleanupDefaultCommands(Application $application): void
    {
        $application->get('help')
            ->setHidden(true);

        PrivatesAccessor::propertyClosure($application, 'commands', static function (array $commands): array {
            // remove default commands, as not needed here
            unset($commands['completion']);

            return $commands;
        });
    }

    private function emulateTokensOfOlderPHP(): void
    {
        // define fallback constants for PHP 8.0 tokens in case of e.g. PHP 7.2 run
        if (! defined('T_MATCH')) {
            define('T_MATCH', 5000);
        }

        if (! defined('T_READONLY')) {
            define('T_READONLY', 5010);
        }

        if (! defined('T_ENUM')) {
            define('T_ENUM', 5015);
        }

        if (! defined('T_COALESCE_EQUAL')) {
            define('T_COALESCE_EQUAL', 5020);
        }

        if (! defined('T_FN')) {
            define('T_FN', 5030);
        }

        if (! defined('T_BAD_CHARACTER')) {
            define('T_BAD_CHARACTER', 5040);
        }
    }
}
