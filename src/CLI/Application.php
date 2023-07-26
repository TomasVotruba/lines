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

        if ($arguments->displayHelp()) {
            echo PHP_EOL;
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

        if ($arguments->isJsonFormat()) {
            $printer = new JsonPrinter();
            $printer->printResult($result);
        } else {
            $textPrinter = new TextPrinter();
            $textPrinter->printResult($result);
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

  --json            Write results in JSON format
EOT;
    }
}
