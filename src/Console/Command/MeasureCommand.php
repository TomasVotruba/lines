<?php

declare (strict_types=1);
namespace Lines202407\TomasVotruba\Lines\Console\Command;

use Lines202407\Symfony\Component\Console\Command\Command;
use Lines202407\Symfony\Component\Console\Input\InputArgument;
use Lines202407\Symfony\Component\Console\Input\InputInterface;
use Lines202407\Symfony\Component\Console\Input\InputOption;
use Lines202407\Symfony\Component\Console\Output\OutputInterface;
use Lines202407\Symfony\Component\Console\Style\SymfonyStyle;
use Lines202407\TomasVotruba\Lines\Analyser;
use Lines202407\TomasVotruba\Lines\Console\OutputFormatter\JsonOutputFormatter;
use Lines202407\TomasVotruba\Lines\Console\OutputFormatter\TextOutputFormatter;
use Lines202407\TomasVotruba\Lines\Finder\PhpFilesFinder;
final class MeasureCommand extends Command
{
    /**
     * @readonly
     * @var \TomasVotruba\Lines\Finder\PhpFilesFinder
     */
    private $phpFilesFinder;
    /**
     * @readonly
     * @var \TomasVotruba\Lines\Analyser
     */
    private $analyser;
    /**
     * @readonly
     * @var \TomasVotruba\Lines\Console\OutputFormatter\JsonOutputFormatter
     */
    private $jsonOutputFormatter;
    /**
     * @readonly
     * @var \TomasVotruba\Lines\Console\OutputFormatter\TextOutputFormatter
     */
    private $textOutputFormatter;
    /**
     * @readonly
     * @var \Symfony\Component\Console\Style\SymfonyStyle
     */
    private $symfonyStyle;
    public function __construct(PhpFilesFinder $phpFilesFinder, Analyser $analyser, JsonOutputFormatter $jsonOutputFormatter, TextOutputFormatter $textOutputFormatter, SymfonyStyle $symfonyStyle)
    {
        $this->phpFilesFinder = $phpFilesFinder;
        $this->analyser = $analyser;
        $this->jsonOutputFormatter = $jsonOutputFormatter;
        $this->textOutputFormatter = $textOutputFormatter;
        $this->symfonyStyle = $symfonyStyle;
        parent::__construct();
    }
    protected function configure() : void
    {
        $this->setName('measure');
        $this->setDescription('Measure lines of code in given path(s)');
        $this->addArgument('paths', InputArgument::REQUIRED | InputArgument::IS_ARRAY, 'Path to analyze');
        $this->addOption('exclude', null, InputOption::VALUE_REQUIRED | InputOption::VALUE_IS_ARRAY, 'Paths to exclude', []);
        $this->addOption('json', null, InputOption::VALUE_NONE, 'Output in JSON format');
        $this->addOption('short', null, InputOption::VALUE_NONE, 'Print short metrics only');
        $this->addOption('allow-vendor', null, InputOption::VALUE_NONE, 'Allow /vendor directory to be scanned');
        $this->addOption('longest', null, InputOption::VALUE_NONE, 'Show top 10 longest files');
    }
    /**
     * @return self::FAILURE|self::SUCCESS
     */
    protected function execute(InputInterface $input, OutputInterface $output) : int
    {
        $paths = (array) $input->getArgument('paths');
        $excludes = (array) $input->getOption('exclude');
        $isJson = (bool) $input->getOption('json');
        $isShort = (bool) $input->getOption('short');
        $allowVendor = (bool) $input->getOption('allow-vendor');
        $showLongestFiles = (bool) $input->getOption('longest');
        $filePaths = $this->phpFilesFinder->findInDirectories($paths, $excludes, $allowVendor);
        if ($filePaths === []) {
            $output->writeln('<error>No files found to scan</error>');
            return Command::FAILURE;
        }
        $progressBarClosure = $this->createProgressBarClosure($isJson, $filePaths);
        $measurements = $this->analyser->measureFiles($filePaths, $progressBarClosure);
        // print results
        if ($isJson) {
            $this->jsonOutputFormatter->printMeasurement($measurements, $isShort, $showLongestFiles);
        } else {
            $this->textOutputFormatter->printMeasurement($measurements, $isShort, $showLongestFiles);
        }
        return Command::SUCCESS;
    }
    /**
     * @param string[] $filePaths
     */
    private function createProgressBarClosure(bool $isJson, array $filePaths) : ?callable
    {
        if ($isJson) {
            return null;
        }
        $progressBar = $this->symfonyStyle->createProgressBar(\count($filePaths));
        $progressBar->start();
        return static function () use($progressBar) : void {
            $progressBar->advance();
        };
    }
}
