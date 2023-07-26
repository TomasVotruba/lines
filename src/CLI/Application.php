<?php

declare(strict_types=1);

namespace TomasVotruba\Lines\CLI;

use Throwable;
use TomasVotruba\Lines\Analyser;
use TomasVotruba\Lines\Enum\StatusCode;
use TomasVotruba\Lines\Log\Json as JsonPrinter;
use TomasVotruba\Lines\Log\Text as TextPrinter;
use TomasVotruba\Lines\PhpFilesFinder;

final class Application
{
    /**
     * @api run in bin file
     * @param mixed[] $argv
     */
    public function run(array $argv): int
    {
        $argumentsBuilder = new ArgumentsBuilder();

        try {
            $arguments = $argumentsBuilder->build($argv);
        } catch (Throwable $throwable) {
            echo PHP_EOL . $throwable->getMessage() . PHP_EOL;

            return StatusCode::ERROR;
        }

        echo PHP_EOL;

        if ($arguments->displayHelp()) {
            $this->help();

            return StatusCode::SUCCESS;
        }

        $phpFilesFinder = new PhpFilesFinder();
        $filePaths = $phpFilesFinder->findInDirectories($arguments->getDirectories(), $arguments->getSuffixes(), $arguments->getExclude());

        if ($filePaths === []) {
            echo 'No files found to scan' . PHP_EOL;
            return StatusCode::ERROR;
        }

        $analyser = new Analyser();
        $result = $analyser->countFiles($filePaths);

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
        echo <<<'EOT'
Usage:
  lines <directory> [options]

  --suffix <suffix> Include files with names ending in <suffix> in the analysis
                    (default: ".ph"p; can be given multiple times)

  --exclude <path>  Exclude files with <path> in their path from the analysis
                    (can be given multiple times)

  --log-json <file> Write results in JSON format to <file>
EOT;
    }
}
