<?php

declare (strict_types=1);
namespace Lines202606\TomasVotruba\Lines\Command;

use Lines202606\Entropy\Console\Contract\CommandInterface;
use Lines202606\Entropy\Console\Enum\ExitCode;
use Lines202606\Symfony\Component\Console\Style\SymfonyStyle;
use Lines202606\TomasVotruba\Lines\Console\OutputFormatter\JsonOutputFormatter;
use Lines202606\TomasVotruba\Lines\FeatureCounter\Analyzer\FeatureCounterAnalyzer;
use Lines202606\TomasVotruba\Lines\FeatureCounter\ResultPrinter;
use Lines202606\TomasVotruba\Lines\Finder\ProjectFilesFinder;
use Lines202606\Webmozart\Assert\Assert;
final class FeaturesCommand implements CommandInterface
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
    }
    public function getName() : string
    {
        return 'features';
    }
    public function getDescription() : string
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
    public function run(string $path = '', bool $json = \false) : int
    {
        $projectDirectory = $path === '' ? (string) \getcwd() : $path;
        Assert::directory($projectDirectory, \sprintf('The directory "%s" does not exist.', $projectDirectory));
        // Find project PHP files in the directory
        $fileInfos = $this->projectFilesFinder->find($projectDirectory);
        // Analyze collected files
        $featureCollector = $this->featureCounterAnalyzer->analyze($fileInfos);
        $this->symfonyStyle->newLine();
        // print results
        if ($json) {
            $this->jsonOutputFormatter->printFeatures($featureCollector);
        } else {
            $this->resultPrinter->print($featureCollector);
        }
        return ExitCode::SUCCESS;
    }
}
