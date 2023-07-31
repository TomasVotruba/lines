<?php

declare (strict_types=1);
namespace Lines202307\TomasVotruba\Lines\Console\Command;

use Lines202307\Symfony\Component\Console\Command\Command;
use Lines202307\Symfony\Component\Console\Input\InputArgument;
use Lines202307\Symfony\Component\Console\Input\InputInterface;
use Lines202307\Symfony\Component\Console\Input\InputOption;
use Lines202307\Symfony\Component\Console\Output\OutputInterface;
use Lines202307\TomasVotruba\Lines\Analyser;
use Lines202307\TomasVotruba\Lines\Console\OutputFormatter\JsonOutputFormatter;
use Lines202307\TomasVotruba\Lines\Console\OutputFormatter\TextOutputFormatter;
use Lines202307\TomasVotruba\Lines\Finder\PhpFilesFinder;
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
    public function __construct(PhpFilesFinder $phpFilesFinder, Analyser $analyser, JsonOutputFormatter $jsonOutputFormatter, TextOutputFormatter $textOutputFormatter)
    {
        $this->phpFilesFinder = $phpFilesFinder;
        $this->analyser = $analyser;
        $this->jsonOutputFormatter = $jsonOutputFormatter;
        $this->textOutputFormatter = $textOutputFormatter;
        parent::__construct();
    }
    protected function configure() : void
    {
        $this->setName('measure');
        $this->setDescription('Measure lines of code in given path(s)');
        $this->addArgument('paths', InputArgument::REQUIRED | InputArgument::IS_ARRAY, 'Path to analyze');
        $this->addOption('exclude', null, InputOption::VALUE_REQUIRED | InputOption::VALUE_IS_ARRAY, 'Paths to exclude', []);
        $this->addOption('json', null, InputOption::VALUE_NONE, 'Output in JSON format');
    }
    /**
     * @return self::FAILURE|self::SUCCESS
     */
    protected function execute(InputInterface $input, OutputInterface $output) : int
    {
        $paths = (array) $input->getArgument('paths');
        $excludes = (array) $input->getOption('exclude');
        $isJson = (bool) $input->getOption('json');
        $filePaths = $this->phpFilesFinder->findInDirectories($paths, $excludes);
        if ($filePaths === []) {
            $output->writeln('<error>No files found to scan</error>');
            return Command::FAILURE;
        }
        $measurement = $this->analyser->measureFiles($filePaths);
        // print results
        if ($isJson) {
            $this->jsonOutputFormatter->printMeasurement($measurement, $output);
        } else {
            $this->textOutputFormatter->printMeasurement($measurement, $output);
        }
        return Command::SUCCESS;
    }
}
