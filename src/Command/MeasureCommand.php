<?php

declare(strict_types=1);

namespace TomasVotruba\Lines\Command;

use Entropy\Console\Contract\CommandInterface;
use Entropy\Console\Enum\ExitCode;
use Symfony\Component\Console\Style\SymfonyStyle;
use TomasVotruba\Lines\Analyser;
use TomasVotruba\Lines\Console\OutputFormatter\JsonOutputFormatter;
use TomasVotruba\Lines\Console\OutputFormatter\TextOutputFormatter;
use TomasVotruba\Lines\Finder\PhpFilesFinder;

final readonly class MeasureCommand implements CommandInterface
{
    public function __construct(
        private PhpFilesFinder $phpFilesFinder,
        private Analyser $analyser,
        private JsonOutputFormatter $jsonOutputFormatter,
        private TextOutputFormatter $textOutputFormatter,
        private SymfonyStyle $symfonyStyle,
    ) {
    }

    public function getName(): string
    {
        return 'measure';
    }

    public function getDescription(): string
    {
        return 'Measure lines of code in given path(s)';
    }

    /**
     * @api invoked dynamically by entropy console
     *
     * @param bool $json Output in JSON format
     * @param string[] $paths Paths to analyze
     * @param string[] $excludes Paths to exclude
     * @param bool $short Print short metrics only
     * @param bool $allowVendor Allow /vendor directory to be scanned
     * @param bool $longest Show top 10 longest files
     * @return ExitCode::*
     */
    public function run(
        bool $json = false,
        array $paths = [],
        array $excludes = [],
        bool $short = false,
        bool $allowVendor = false,
        bool $longest = false,
    ): int {
        if ($paths === []) {
            $paths = [(string) getcwd()];
        }

        $filePaths = $this->phpFilesFinder->findInDirectories($paths, $excludes, $allowVendor);
        if ($filePaths === []) {
            $this->symfonyStyle->error('No files found to scan');
            return ExitCode::ERROR;
        }

        $progressBarClosure = $this->createProgressBarClosure($json, $filePaths);
        $measurements = $this->analyser->measureFiles($filePaths, $progressBarClosure);

        // print results
        if ($json) {
            $this->jsonOutputFormatter->printMeasurement($measurements, $short, $longest);
        } else {
            $this->textOutputFormatter->printMeasurement($measurements, $short, $longest);
        }

        return ExitCode::SUCCESS;
    }

    /**
     * @param string[] $filePaths
     */
    private function createProgressBarClosure(bool $isJson, array $filePaths): ?callable
    {
        if ($isJson) {
            return null;
        }

        $progressBar = $this->symfonyStyle->createProgressBar(count($filePaths));
        $progressBar->start();

        return static function () use ($progressBar): void {
            $progressBar->advance();
        };
    }
}
