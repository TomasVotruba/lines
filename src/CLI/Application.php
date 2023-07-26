<?php

declare(strict_types=1);

namespace TomasVotruba\Lines\CLI;

use TomasVotruba\Lines\ArgumentsBuilder;
use TomasVotruba\Lines\Log\Json as JsonPrinter;
use TomasVotruba\Lines\Log\Text as TextPrinter;
use SebastianBergmann\FileIterator\Facade;

final class Application
{
    public function run(array $argv): int
    {
        try {
            $arguments = (new ArgumentsBuilder())->build($argv);
        } catch (Exception $e) {
            print PHP_EOL . $e->getMessage() . PHP_EOL;

            return 1;
        }

        print PHP_EOL;

        if ($arguments->help()) {
            $this->help();

            return 0;
        }

        $files = (new Facade)->getFilesAsArray(
            $arguments->directories(),
            $arguments->suffixes(),
            '',
            $arguments->exclude()
        );

        if (empty($files)) {
            print 'No files found to scan' . PHP_EOL;

            return 1;
        }

        $result = (new Analyser)->countFiles($files, $arguments->countTests());

        (new TextPrinter)->printResult($result, $arguments->countTests());

        if ($arguments->jsonLogfile()) {
            $printer = new JsonPrinter;

            $printer->printResult($arguments->jsonLogfile(), $result);
        }

        return 0;
    }

    private function help(): void
    {
        print <<<'EOT'
Usage:
  phploc [options] <directory>

Options for selecting files:

  --suffix <suffix> Include files with names ending in <suffix> in the analysis
                    (default: .php; can be given multiple times)
  --exclude <path>  Exclude files with <path> in their path from the analysis
                    (can be given multiple times)

Options for analysing files:

  --count-tests     Count PHPUnit test case classes and test methods

Options for report generation:

  --log-json <file> Write results in JSON format to <file>

EOT;
    }
}
