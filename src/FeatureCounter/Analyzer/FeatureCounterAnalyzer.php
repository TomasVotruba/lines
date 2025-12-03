<?php

declare(strict_types=1);

namespace TomasVotruba\Lines\FeatureCounter\Analyzer;

use PhpParser\NodeTraverser;
use PhpParser\Parser;
use PhpParser\ParserFactory;
use Symfony\Component\Console\Helper\ProgressBar;
use Symfony\Component\Console\Output\ConsoleOutput;
use Symfony\Component\Finder\SplFileInfo;
use TomasVotruba\Lines\FeatureCounter\Exception\InvalidStateException;
use TomasVotruba\Lines\FeatureCounter\NodeVisitor\NodeInstanceNodeVisitor;
use TomasVotruba\Lines\FeatureCounter\NodeVisitor\PatternTriggerNodeVisitor;
use TomasVotruba\Lines\FeatureCounter\ValueObject\FeatureCollector;

final class FeatureCounterAnalyzer
{
    private Parser $parser;

    public function __construct()
    {
        $parserFactory = new ParserFactory();
        $this->parser = $parserFactory->createForNewestSupportedVersion();
    }

    /**
     * @param SplFileInfo[] $fileInfos
     */
    public function analyze(array $fileInfos): FeatureCollector
    {
        $progressBar = new ProgressBar(new ConsoleOutput());
        $progressBar->start(count($fileInfos));

        $featureCollector = new FeatureCollector();

        $nodeTraverser = $this->createNodeTraverser($featureCollector);

        foreach ($fileInfos as $fileInfo) {
            $stmts = $this->parser->parse($fileInfo->getContents());
            if ($stmts === null) {
                throw new InvalidStateException(sprintf(
                    'Parsing of file "%s" resulted in null statements.',
                    $fileInfo->getRealPath()
                ));
            }

            $nodeTraverser->traverse($stmts);

            $progressBar->advance();
        }

        $progressBar->finish();

        return $featureCollector;
    }

    private function createNodeTraverser(FeatureCollector $featureCollector): NodeTraverser
    {
        $featureCountingNodeVisitor = new PatternTriggerNodeVisitor($featureCollector);
        $nodeInstanceNodeVisitor = new NodeInstanceNodeVisitor($featureCollector);

        return new NodeTraverser(...[$featureCountingNodeVisitor, $nodeInstanceNodeVisitor]);
    }
}
