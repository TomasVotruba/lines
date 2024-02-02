<?php

declare(strict_types=1);

namespace TomasVotruba\Lines\Console\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use TomasVotruba\Lines\Analyser;
use TomasVotruba\Lines\Console\OutputFormatter\JsonOutputFormatter;
use TomasVotruba\Lines\Console\OutputFormatter\TextOutputFormatter;
use TomasVotruba\Lines\Finder\PhpFilesFinder;

final class MeasureCommand extends Command
{
    public function __construct(
        private readonly PhpFilesFinder $phpFilesFinder,
        private readonly Analyser $analyser,
        private readonly JsonOutputFormatter $jsonOutputFormatter,
        private readonly TextOutputFormatter $textOutputFormatter,
        private readonly SymfonyStyle $symfonyStyle,
    ) {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this->setName('measure');
        $this->setDescription('Measure lines of code in given path(s)');

        $this->addArgument('paths', InputArgument::REQUIRED | InputArgument::IS_ARRAY, 'Path to analyze');
        $this->addOption(
            'exclude',
            null,
            InputOption::VALUE_REQUIRED | InputOption::VALUE_IS_ARRAY,
            'Paths to exclude',
            []
        );

        $this->addOption('json', null, InputOption::VALUE_NONE, 'Output in JSON format');
        $this->addOption('short', null, InputOption::VALUE_NONE, 'Print short metrics only');
        $this->addOption('allow-vendor', null, InputOption::VALUE_NONE, 'Allow /vendor directory to be scanned');
    }

    /**
     * @return self::FAILURE|self::SUCCESS
     */
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $paths = (array) $input->getArgument('paths');
        $excludes = (array) $input->getOption('exclude');
        $isJson = (bool) $input->getOption('json');
        $isShort = (bool) $input->getOption('short');
        $allowVendor = (bool) $input->getOption('allow-vendor');

        $filePaths = $this->phpFilesFinder->findInDirectories($paths, $excludes, $allowVendor);
        if ($filePaths === []) {
            $output->writeln('<error>No files found to scan</error>');
            return Command::FAILURE;
        }

        $progressBarClosure = $this->createProgressBarClosure($isJson, $filePaths);
        $measurement = $this->analyser->measureFiles($filePaths, $progressBarClosure);

        // print results
        if ($isJson) {
            $this->jsonOutputFormatter->printMeasurement($measurement, $isShort);
        } else {
            $this->textOutputFormatter->printMeasurement($measurement, $isShort);
        }

        return Command::SUCCESS;
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
