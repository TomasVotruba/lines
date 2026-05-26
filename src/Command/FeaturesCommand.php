<?php

declare (strict_types=1);
namespace Lines202605\TomasVotruba\Lines\Command;

use Lines202605\Symfony\Component\Console\Command\Command;
use Lines202605\Symfony\Component\Console\Input\InputArgument;
use Lines202605\Symfony\Component\Console\Input\InputInterface;
use Lines202605\Symfony\Component\Console\Input\InputOption;
use Lines202605\Symfony\Component\Console\Output\OutputInterface;
use Lines202605\Symfony\Component\Console\Style\SymfonyStyle;
use Lines202605\TomasVotruba\Lines\Console\OutputFormatter\JsonOutputFormatter;
use Lines202605\TomasVotruba\Lines\FeatureCounter\Analyzer\FeatureCounterAnalyzer;
use Lines202605\TomasVotruba\Lines\FeatureCounter\ResultPrinter;
use Lines202605\TomasVotruba\Lines\Finder\ProjectFilesFinder;
use Lines202605\Webmozart\Assert\Assert;
final class FeaturesCommand extends Command
{
    /**
     * @readonly
     * @var \Symfony\Component\Console\Style\SymfonyStyle
     */
    private $symfonyStyle;
    /**
     * @readonly
     * @var \TomasVotruba\Lines\Finder\ProjectFilesFinder
     */
    private $projectFilesFinder;
    /**
     * @readonly
     * @var \TomasVotruba\Lines\FeatureCounter\Analyzer\FeatureCounterAnalyzer
     */
    private $featureCounterAnalyzer;
    /**
     * @readonly
     * @var \TomasVotruba\Lines\FeatureCounter\ResultPrinter
     */
    private $resultPrinter;
    /**
     * @readonly
     * @var \TomasVotruba\Lines\Console\OutputFormatter\JsonOutputFormatter
     */
    private $jsonOutputFormatter;
    public function __construct(SymfonyStyle $symfonyStyle, ProjectFilesFinder $projectFilesFinder, FeatureCounterAnalyzer $featureCounterAnalyzer, ResultPrinter $resultPrinter, JsonOutputFormatter $jsonOutputFormatter)
    {
        $this->symfonyStyle = $symfonyStyle;
        $this->projectFilesFinder = $projectFilesFinder;
        $this->featureCounterAnalyzer = $featureCounterAnalyzer;
        $this->resultPrinter = $resultPrinter;
        $this->jsonOutputFormatter = $jsonOutputFormatter;
        parent::__construct();
    }
    protected function configure() : void
    {
        $this->setName('features');
        $this->setDescription('Count used PHP features in the project');
        $this->addArgument('project-directories', InputArgument::IS_ARRAY | InputArgument::OPTIONAL, 'Project directories to analyze', [\getcwd()]);
        $this->addOption('json', null, InputOption::VALUE_NONE, 'Output in JSON format');
    }
    /**
     * @return self::FAILURE|self::SUCCESS
     */
    protected function execute(InputInterface $input, OutputInterface $output) : int
    {
        $projectDirectories = $input->getArgument('project-directories');
        Assert::isArray($projectDirectories);
        $allFileInfos = [];
        foreach ($projectDirectories as $projectDirectory) {
            Assert::string($projectDirectory, 'Expected a string for project directory.');
            Assert::directory($projectDirectory, \sprintf('The directory "%s" does not exist.', $projectDirectory));
            // Find project PHP files in the directory
            $fileInfos = $this->projectFilesFinder->find($projectDirectory);
            $allFileInfos = \array_merge($allFileInfos, $fileInfos);
        }
        $isJson = (bool) $input->getOption('json');
        // Analyze collected files
        $featureCollector = $this->featureCounterAnalyzer->analyze($allFileInfos);
        $this->symfonyStyle->newLine();
        // print results
        if ($isJson) {
            $this->jsonOutputFormatter->printFeatures($featureCollector);
        } else {
            $this->resultPrinter->print($featureCollector);
        }
        return Command::SUCCESS;
    }
}
