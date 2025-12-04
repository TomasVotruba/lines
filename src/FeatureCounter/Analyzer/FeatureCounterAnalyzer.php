<?php

declare(strict_types=1);

namespace TomasVotruba\Lines\FeatureCounter\Analyzer;

use PhpParser\NodeTraverser;
use PhpParser\Parser;
use PhpParser\ParserFactory;
use Symfony\Component\Console\Helper\ProgressBar;
use Symfony\Component\Console\Output\ConsoleOutput;
use Symfony\Component\Finder\SplFileInfo;
use TomasVotruba\Lines\Exception\ShouldNotHappenException;
use TomasVotruba\Lines\FeatureCounter\NodeVisitor\FeatureCollectorNodeVisitor;
use TomasVotruba\Lines\FeatureCounter\ValueObject\FeatureCollector;

/**
 * @see \TomasVotruba\Lines\Tests\FeatureCounter\Analyzer\FeatureCounterAnalyzerTest
 */
final readonly class FeatureCounterAnalyzer
{
    private Parser $parser;

    public function __construct(
        private FeatureCollector $featureCollector,
    ) {
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

        $featureCollectorNodeVisitor = new FeatureCollectorNodeVisitor($this->featureCollector);
        $nodeTraverser = new NodeTraverser($featureCollectorNodeVisitor);

        foreach ($fileInfos as $fileInfo) {
            $stmts = $this->parser->parse($fileInfo->getContents());
            if ($stmts === null) {
                throw new ShouldNotHappenException(sprintf(
                    'Parsing of file "%s" resulted in null statements.',
                    $fileInfo->getRealPath()
                ));
            }

            $nodeTraverser->traverse($stmts);

            $progressBar->advance();
        }

        $progressBar->finish();

        return $this->featureCollector;
    }
}
