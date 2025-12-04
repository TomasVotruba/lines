<?php

declare (strict_types=1);
namespace Lines202512\TomasVotruba\Lines\Command;

use Lines202512\Symfony\Component\Console\Command\Command;
use Lines202512\Symfony\Component\Console\Input\InputArgument;
use Lines202512\Symfony\Component\Console\Input\InputInterface;
use Lines202512\Symfony\Component\Console\Input\InputOption;
use Lines202512\Symfony\Component\Console\Output\OutputInterface;
use Lines202512\Symfony\Component\Console\Style\SymfonyStyle;
use Lines202512\TomasVotruba\Lines\FeatureCounter\Analyzer\FeatureCounterAnalyzer;
use Lines202512\TomasVotruba\Lines\FeatureCounter\ResultPrinter;
use Lines202512\TomasVotruba\Lines\Finder\ProjectFilesFinder;
use Lines202512\Webmozart\Assert\Assert;
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
    public function __construct(SymfonyStyle $symfonyStyle, ProjectFilesFinder $projectFilesFinder, FeatureCounterAnalyzer $featureCounterAnalyzer, ResultPrinter $resultPrinter)
    {
        $this->symfonyStyle = $symfonyStyle;
        $this->projectFilesFinder = $projectFilesFinder;
        $this->featureCounterAnalyzer = $featureCounterAnalyzer;
        $this->resultPrinter = $resultPrinter;
        parent::__construct();
    }
    protected function configure() : void
    {
        $this->setName('features');
        $this->setDescription('Count used PHP features in the project');
        $this->addArgument('project-directory', InputArgument::OPTIONAL, 'Project directory to analyze', [\getcwd()]);
        $this->addOption('json', null, InputOption::VALUE_NONE, 'Output in JSON format');
    }
    /**
     * @return self::FAILURE|self::SUCCESS
     */
    protected function execute(InputInterface $input, OutputInterface $output) : int
    {
        $projectDirectory = $input->getArgument('project-directory');
        Assert::string($projectDirectory);
        Assert::directory($projectDirectory);
        $input->getOption('json');
        // find project PHP files
        $fileInfos = $this->projectFilesFinder->find($projectDirectory);
        $featureCollector = $this->featureCounterAnalyzer->analyze($fileInfos);
        $this->symfonyStyle->newLine();
        $this->resultPrinter->print($featureCollector);
        return Command::SUCCESS;
    }
}
