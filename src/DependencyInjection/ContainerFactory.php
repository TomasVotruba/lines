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
use TomasVotruba\Lines\Console\Command\MeasureCommand;
use TomasVotruba\Lines\Console\Command\VendorCommand;

final class ContainerFactory
{
    /**
     * @api used in bin and tests
     */
    public function create(): Container
    {
        $container = new Container();

        $this->emulateTokensOfOlderPHP();

        // console
        $container->singleton(
            SymfonyStyle::class,
            static fn (): SymfonyStyle => new SymfonyStyle(new ArrayInput([]), new ConsoleOutput())
        );

        $container->singleton(Application::class, static function (Container $container): Application {
            $application = new Application();
            $commands = [];
            $commands[] = $container->make(MeasureCommand::class);
            $commands[] = $container->make(VendorCommand::class);
            $application->addCommands($commands);
            return $application;
        });

        // parser
        $container->singleton(Parser::class, static function (): Parser {
            $phpParserFactory = new ParserFactory();
            return $phpParserFactory->create(ParserFactory::PREFER_PHP7);
        });

        return $container;
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
    }
}
