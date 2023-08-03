<?php

declare (strict_types=1);
namespace Lines202308\TomasVotruba\Lines\DependencyInjection;

use Lines202308\Illuminate\Container\Container;
use Lines202308\PhpParser\Parser;
use Lines202308\PhpParser\ParserFactory;
use Lines202308\Symfony\Component\Console\Application;
use Lines202308\Symfony\Component\Console\Input\ArrayInput;
use Lines202308\Symfony\Component\Console\Output\ConsoleOutput;
use Lines202308\Symfony\Component\Console\Style\SymfonyStyle;
use Lines202308\TomasVotruba\Lines\Console\Command\MeasureCommand;
use Lines202308\TomasVotruba\Lines\Console\Command\VendorCommand;
final class ContainerFactory
{
    /**
     * @api used in bin and tests
     */
    public function create() : Container
    {
        $container = new Container();
        // console
        $container->singleton(SymfonyStyle::class, static function () : SymfonyStyle {
            return new SymfonyStyle(new ArrayInput([]), new ConsoleOutput());
        });
        $container->singleton(Application::class, static function (Container $container) : Application {
            $application = new Application();
            $commands = [];
            $commands[] = $container->make(MeasureCommand::class);
            $commands[] = $container->make(VendorCommand::class);
            $application->addCommands($commands);
            return $application;
        });
        // parser
        $container->singleton(Parser::class, static function () : Parser {
            $phpParserFactory = new ParserFactory();
            return $phpParserFactory->create(ParserFactory::PREFER_PHP7);
        });
        return $container;
    }
}
