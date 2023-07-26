<?php declare(strict_types=1);
namespace TomasVotruba\Lines;

use SebastianBergmann\CliParser\Exception as CliParserException;
use SebastianBergmann\CliParser\Parser as CliParser;

final class ArgumentsBuilder
{
    /**
     * @throws ArgumentsBuilderException
     */
    public function build(array $argv): Arguments
    {
        try {
            $options = (new CliParser)->parse(
                $argv,
                'hv',
                [
                    'suffix=',
                    'exclude=',
                    'count-tests',
                    'log-json=',
                    'help',
                    'version',
                ]
            );
        } catch (CliParserException $e) {
            throw new ArgumentsBuilderException(
                $e->getMessage(),
                (int) $e->getCode(),
                $e
            );
        }

        $directories = $options[1];
        $exclude     = [];
        $suffixes    = ['.php'];
        $countTests  = false;
        $jsonLogfile = null;
        $help        = false;
        $version     = false;

        foreach ($options[0] as $option) {
            switch ($option[0]) {
                case '--suffix':
                    $suffixes[] = $option[1];

                    break;

                case '--exclude':
                    $exclude[] = $option[1];

                    break;

                case '--count-tests':
                    $countTests = true;

                    break;

                case '--log-json':
                    $jsonLogfile = $option[1];

                    break;

                case 'h':
                case '--help':
                    $help = true;

                    break;

                case 'v':
                case '--version':
                    $version = true;

                    break;
            }
        }

        if (empty($options[1]) && !$help && !$version) {
            throw new ArgumentsBuilderException(
                'No directory specified'
            );
        }

        return new Arguments(
            $directories,
            $suffixes,
            $exclude,
            $countTests,
            $jsonLogfile,
            $help,
            $version,
        );
    }
}
