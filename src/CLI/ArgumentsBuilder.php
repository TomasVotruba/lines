<?php

declare(strict_types=1);

namespace TomasVotruba\Lines\CLI;

use SebastianBergmann\CliParser\Exception as CliParserException;
use SebastianBergmann\CliParser\Parser as CliParser;
use TomasVotruba\Lines\Exception\ShouldNotHappenException;

final class ArgumentsBuilder
{
    /**
     * @param mixed[] $argv
     */
    public function build(array $argv): Arguments
    {
        try {
            $options = (new CliParser())->parse(
                $argv,
                '',
                [
                    'suffix=',
                    'exclude=',
                    'log-json=',
                    'help',
                ]
            );
        } catch (CliParserException $cliParserException) {
            throw new ShouldNotHappenException(
                $cliParserException->getMessage(),
                $cliParserException->getCode(),
                $cliParserException
            );
        }

        $directories = $options[1];
        $exclude = [];
        $suffixes = ['.php'];
        $jsonLogfile = null;
        $help = false;

        foreach ($options[0] as $option) {
            switch ($option[0]) {
                case '--suffix':
                    $suffixes[] = $option[1];

                    break;

                case '--exclude':
                    $exclude[] = $option[1];

                    break;

                case '--log-json':
                    $jsonLogfile = $option[1];

                    break;

                case 'h':
                case '--help':
                    $help = true;

                    break;
            }
        }

        if (empty($options[1]) && ! $help) {
            throw new ShouldNotHappenException('No directory specified');
        }

        return new Arguments(
            $directories,
            $suffixes,
            $exclude,
            $jsonLogfile,
            $help,
        );
    }
}
