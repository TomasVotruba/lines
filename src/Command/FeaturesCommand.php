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
        private SymfonyStyle $symfonyStyle,
        private readonly ProjectFilesFinder $projectFilesFinder,
        private readonly FeatureCounterAnalyzer $featureCounterAnalyzer,
<<<<<<< HEAD
        private readonly ResultPrinter $resultPrinter
=======
        private readonly ResultPrinter $resultPrinter,
        private readonly JsonOutputFormatter $jsonOutputFormatter,
>>>>>>> 3807e5e (implement first POC to resolve issue #63 (#65))
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

        $isJson = (bool) $input->getOption('json');

        // Analyze collected files
        $featureCollector = $this->featureCounterAnalyzer->analyze($allFileInfos);

        $this->symfonyStyle->newLine();
        $this->symfonyStyle->title('PHP features');

        // print results
        if ($isJson) {
            $this->jsonOutputFormatter->printFeatures($featureCollector);
        } else {
            $this->resultPrinter->print($featureCollector, $this->symfonyStyle);
        }

        return Command::SUCCESS;
    }
}
