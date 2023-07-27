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
use TomasVotruba\Lines\PhpFilesFinder;

final class MeasureCommand extends Command
{
    private readonly PhpFilesFinder $phpFilesFinder;

    private readonly Analyser $analyser;

    public function __construct()
    {
        parent::__construct();

        $this->phpFilesFinder = new PhpFilesFinder();
        $this->analyser = new Analyser();
    }

    protected function configure(): void
    {
        $this->setName('measure');

        $this->setDescription('Measure lines of code in given path(s)');

        $this->addArgument('paths', InputArgument::REQUIRED | InputArgument::IS_ARRAY, 'Path to analyze');
        $this->addOption(
            'suffix',
            null,
            InputOption::VALUE_OPTIONAL | InputOption::VALUE_IS_ARRAY,
            'Suffix of files to analyze',
            ['php']
        );
        $this->addOption(
            'exclude',
            null,
            InputOption::VALUE_REQUIRED | InputOption::VALUE_IS_ARRAY,
            'Paths to exclude',
            []
        );
        $this->addOption('json', null, InputOption::VALUE_NONE, 'Output in JSON format');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $symfonyStyle = new SymfonyStyle($input, $output);

        $paths = $input->getArgument('paths');
        $suffixes = $input->getOption('suffix');
        $excludes = $input->getOption('exclude');
        $isJson = (bool) $input->getOption('json');

        $filePaths = $this->phpFilesFinder->findInDirectories($paths, $suffixes, $excludes);

        if ($filePaths === []) {
            $output->writeln('<error>No files found to scan</error>');
            return Command::FAILURE;
        }

        $measurmentResult = $this->analyser->measureFiles($filePaths);

        // print results
        if ($isJson) {
            $jsonOutputFormatter = new JsonOutputFormatter();
            $jsonOutputFormatter->printResult($measurmentResult, $output);
        } else {
            $textOutputFormatter = new TextOutputFormatter($symfonyStyle);
            $textOutputFormatter->printResult($measurmentResult, $output);
        }

        return Command::SUCCESS;
    }
}
