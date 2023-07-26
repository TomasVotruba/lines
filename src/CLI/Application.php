<?php

declare(strict_types=1);

namespace TomasVotruba\Lines\CLI;

use SebastianBergmann\FileIterator\Facade;
use TomasVotruba\Lines\Analyser;
use TomasVotruba\Lines\Enum\StatusCode;
use TomasVotruba\Lines\Log\Json as JsonPrinter;
use TomasVotruba\Lines\Log\Text as TextPrinter;

final class Application
{
    /**
     * @param mixed[] $argv
     */
    public function run(array $argv): int
    {
        $argumentsBuilder = new ArgumentsBuilder();

        try {
            $arguments = $argumentsBuilder->build($argv);
        } catch (\Throwable $throwable) {
            print PHP_EOL . $throwable->getMessage() . PHP_EOL;

            return StatusCode::ERROR;
        }

        print PHP_EOL;

        if ($arguments->help()) {
            $this->help();

            return StatusCode::SUCCESS;
        }

        $files = (new Facade())->getFilesAsArray(
            $arguments->directories(),
            $arguments->suffixes(),
            '',
            $arguments->exclude()
        );

        if ($files === []) {
            print 'No files found to scan' . PHP_EOL;
            return StatusCode::ERROR;
        }

        $analyser = new Analyser();
        $result = $analyser->countFiles($files);

        $textPrinter = new TextPrinter();
        $textPrinter->printResult($result);

        if ($arguments->jsonLogfile()) {
            $printer = new JsonPrinter();
            $printer->printResult($arguments->jsonLogfile(), $result);
        }

        return StatusCode::SUCCESS;
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

Options for report generation:

  --log-json <file> Write results in JSON format to <file>
EOT;
    }
}
