<?php

declare(strict_types=1);

namespace TomasVotruba\Lines\Command;

use Entropy\Console\Contract\CommandInterface;
use Entropy\Console\Enum\ExitCode;
use Entropy\Console\Output\OutputPrinter;
use TomasVotruba\Lines\Console\OutputFormatter\JsonOutputFormatter;
use TomasVotruba\Lines\Console\OutputFormatter\TextOutputFormatter;
use TomasVotruba\Lines\FeatureCounter\Analyzer\FeatureCounterAnalyzer;
use TomasVotruba\Lines\Finder\ProjectFilesFinder;
use Webmozart\Assert\Assert;

final readonly class FeaturesCommand implements CommandInterface
{
    public function __construct(
        private OutputPrinter $outputPrinter,
        private ProjectFilesFinder $projectFilesFinder,
        private FeatureCounterAnalyzer $featureCounterAnalyzer,
        private TextOutputFormatter $textOutputFormatter,
        private JsonOutputFormatter $jsonOutputFormatter,
    ) {
    }

    public function getName(): string
    {
        return 'features';
    }

    public function getDescription(): string
    {
        return 'Count used PHP features in the project';
    }

    /**
     * @api invoked dynamically by entropy console
     *
     * @param string $path Project directory to analyze
     * @param bool $json Output in JSON format
     * @return ExitCode::*
     */
    public function run(string $path = '', bool $json = false): int
    {
        $projectDirectory = $path === '' ? (string) getcwd() : $path;
        Assert::directory($projectDirectory, sprintf('The directory "%s" does not exist.', $projectDirectory));

        // Find project PHP files in the directory
        $fileInfos = $this->projectFilesFinder->find($projectDirectory);

        // Analyze collected files
        $featureCollector = $this->featureCounterAnalyzer->analyze($fileInfos);

        $this->outputPrinter->newline();

        // print results
        if ($json) {
            $this->jsonOutputFormatter->printFeatures($featureCollector);
        } else {
            $this->textOutputFormatter->printFeatures($featureCollector);
        }

        return ExitCode::SUCCESS;
    }
}
