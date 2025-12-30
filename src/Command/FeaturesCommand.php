<?php

declare(strict_types=1);

namespace TomasVotruba\Lines\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use TomasVotruba\Lines\FeatureCounter\Analyzer\FeatureCounterAnalyzer;
use TomasVotruba\Lines\FeatureCounter\ResultPrinter;
use TomasVotruba\Lines\Finder\ProjectFilesFinder;
use Webmozart\Assert\Assert;

final class FeaturesCommand extends Command
{
    public function __construct(
        private SymfonyStyle $symfonyStyle,
        private readonly ProjectFilesFinder $projectFilesFinder,
        private readonly FeatureCounterAnalyzer $featureCounterAnalyzer,
        private readonly ResultPrinter $resultPrinter
    ) {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this->setName('features');
        $this->setDescription('Count used PHP features in the project');

        $this->addArgument(
            'project-directories',
            InputArgument::IS_ARRAY | InputArgument::OPTIONAL,
            'Project directories to analyze',
            [getcwd()]
        );
        $this->addOption('json', null, InputOption::VALUE_NONE, 'Output in JSON format');
    }

    /**
     * @return self::FAILURE|self::SUCCESS
     */
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $this->symfonyStyle = new SymfonyStyle($input, $output);
        $projectDirectories = $input->getArgument('project-directories');
        Assert::isArray($projectDirectories);

        $allFileInfos = [];
        foreach ($projectDirectories as $projectDirectory) {
            Assert::string($projectDirectory, 'Expected a string for project directory.');
            Assert::directory($projectDirectory, sprintf('The directory "%s" does not exist.', $projectDirectory));

            // Find project PHP files in the directory
            $fileInfos = $this->projectFilesFinder->find($projectDirectory);
            $allFileInfos = array_merge($allFileInfos, $fileInfos);
        }

        $input->getOption('json');

        // Analyze collected files
        $featureCollector = $this->featureCounterAnalyzer->analyze($allFileInfos);

        $this->symfonyStyle->title('PHP features');
        $this->resultPrinter->setSymfonyStyle($this->symfonyStyle);
        $this->resultPrinter->print($featureCollector);

        return Command::SUCCESS;
    }
}
