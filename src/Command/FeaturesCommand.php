<?php

declare(strict_types=1);

namespace TomasVotruba\Lines\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use TomasVotruba\Lines\Console\OutputFormatter\JsonOutputFormatter;
use TomasVotruba\Lines\FeatureCounter\Analyzer\FeatureCounterAnalyzer;
use TomasVotruba\Lines\FeatureCounter\ResultPrinter;
use TomasVotruba\Lines\Finder\ProjectFilesFinder;
use Webmozart\Assert\Assert;

final class FeaturesCommand extends Command
{
    public function __construct(
        private readonly SymfonyStyle $symfonyStyle,
        private readonly ProjectFilesFinder $projectFilesFinder,
        private readonly FeatureCounterAnalyzer $featureCounterAnalyzer,
        private readonly ResultPrinter $resultPrinter,
        private readonly JsonOutputFormatter $jsonOutputFormatter,
    ) {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this->setName('features');
        $this->setDescription('Count used PHP features in the project');

        $this->addArgument('project-directory', InputArgument::OPTIONAL, 'Project directory to analyze', [getcwd()]);
        $this->addOption('json', null, InputOption::VALUE_NONE, 'Output in JSON format');
    }

    /**
     * @return self::FAILURE|self::SUCCESS
     */
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $projectDirectory = $input->getArgument('project-directory');
        Assert::string($projectDirectory);
        Assert::directory($projectDirectory);

        $isJson = (bool) $input->getOption('json');

        // find project PHP files
        $fileInfos = $this->projectFilesFinder->find($projectDirectory);
        $featureCollector = $this->featureCounterAnalyzer->analyze($fileInfos);

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
