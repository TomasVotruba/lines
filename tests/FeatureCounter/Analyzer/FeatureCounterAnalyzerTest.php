<?php

declare(strict_types=1);

namespace TomasVotruba\Lines\Tests\FeatureCounter\Analyzer;

use PHPUnit\Framework\TestCase;
use TomasVotruba\Lines\DependencyInjection\ContainerFactory;
use TomasVotruba\Lines\FeatureCounter\Analyzer\FeatureCounterAnalyzer;
use TomasVotruba\Lines\Finder\PhpFilesFinder;
use TomasVotruba\Lines\Finder\ProjectFilesFinder;

final class FeatureCounterAnalyzerTest extends TestCase
{
    private FeatureCounterAnalyzer $featureCounterAnalyzer;

    protected function setUp(): void
    {
        $containerFactory = new ContainerFactory();
        $container = $containerFactory->create();

        $this->featureCounterAnalyzer = $container->make(FeatureCounterAnalyzer::class);
    }

    public function test(): void
    {
        $projectFilesFinder = new ProjectFilesFinder();
        $fileInfos = $projectFilesFinder->find(__DIR__ . '/Fixture');

        $featureCollector = $this->featureCounterAnalyzer->analyze($fileInfos);

        dump($featureCollector);
        die;
    }
}
