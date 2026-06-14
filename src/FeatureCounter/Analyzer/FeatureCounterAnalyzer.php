<?php

declare (strict_types=1);
namespace Lines202606\TomasVotruba\Lines\FeatureCounter\Analyzer;

use Lines202606\OndraM\CiDetector\CiDetector;
use Lines202606\PhpParser\NodeTraverser;
use Lines202606\PhpParser\Parser;
use Lines202606\PhpParser\ParserFactory;
use Lines202606\Symfony\Component\Console\Helper\ProgressBar;
use Lines202606\Symfony\Component\Console\Output\ConsoleOutput;
use Lines202606\Symfony\Component\Console\Output\NullOutput;
use Lines202606\Symfony\Component\Finder\SplFileInfo;
use Lines202606\TomasVotruba\Lines\Exception\ShouldNotHappenException;
use Lines202606\TomasVotruba\Lines\FeatureCounter\NodeVisitor\FeatureCollectorNodeVisitor;
use Lines202606\TomasVotruba\Lines\FeatureCounter\ValueObject\FeatureCollector;
/**
 * @see \TomasVotruba\Lines\Tests\FeatureCounter\Analyzer\FeatureCounterAnalyzerTest
 */
final class FeatureCounterAnalyzer
{
    /**
     * @readonly
     * @var \TomasVotruba\Lines\FeatureCounter\ValueObject\FeatureCollector
     */
    private $featureCollector;
    /**
     * @readonly
     * @var \PhpParser\Parser
     */
    private $parser;
    public function __construct(FeatureCollector $featureCollector)
    {
        $this->featureCollector = $featureCollector;
        $parserFactory = new ParserFactory();
        $this->parser = $parserFactory->createForNewestSupportedVersion();
    }
    /**
     * @param SplFileInfo[] $fileInfos
     */
    public function analyze(array $fileInfos) : FeatureCollector
    {
        if ((new CiDetector())->isCiDetected()) {
            $output = new NullOutput();
        } else {
            $output = new ConsoleOutput();
        }
        $progressBar = new ProgressBar($output);
        $progressBar->start(\count($fileInfos));
        $featureCollectorNodeVisitor = new FeatureCollectorNodeVisitor($this->featureCollector);
        $nodeTraverser = new NodeTraverser($featureCollectorNodeVisitor);
        foreach ($fileInfos as $fileInfo) {
            $stmts = $this->parser->parse($fileInfo->getContents());
            if ($stmts === null) {
                throw new ShouldNotHappenException(\sprintf('Parsing of file "%s" resulted in null statements.', $fileInfo->getRealPath()));
            }
            $nodeTraverser->traverse($stmts);
            $progressBar->advance();
        }
        $progressBar->finish();
        return $this->featureCollector;
    }
}
