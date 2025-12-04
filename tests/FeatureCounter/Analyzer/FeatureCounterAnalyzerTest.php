<?php

declare(strict_types=1);

namespace TomasVotruba\Lines\Tests\FeatureCounter\Analyzer;

use PHPUnit\Framework\TestCase;
use TomasVotruba\Lines\DependencyInjection\ContainerFactory;
use TomasVotruba\Lines\FeatureCounter\Analyzer\FeatureCounterAnalyzer;
use TomasVotruba\Lines\FeatureCounter\Enum\FeatureName;
use TomasVotruba\Lines\Finder\ProjectFilesFinder;

final class FeatureCounterAnalyzerTest extends TestCase
{
    private FeatureCounterAnalyzer $featureCounterAnalyzer;

    private ProjectFilesFinder $projectFilesFinder;

    protected function setUp(): void
    {
        $containerFactory = new ContainerFactory();
        $container = $containerFactory->create();

        $this->featureCounterAnalyzer = $container->make(FeatureCounterAnalyzer::class);

        $this->projectFilesFinder = new ProjectFilesFinder();
    }

    public function test(): void
    {
        $fileInfos = $this->projectFilesFinder->find(__DIR__ . '/Fixture');
        $featureCollector = $this->featureCounterAnalyzer->analyze($fileInfos);

        $featuresCountedByPhpVersion = $featureCollector->getGroupedFeatureCountedByPhpVersion();

        // uion types

        // property type

        // strict declares
        dump($featuresCountedByPhpVersion['7.0'][FeatureName::STRICT_DECLARES]);
        dump($featuresCountedByPhpVersion['7.4'][FeatureName::TYPED_PROPERTIES]);
        dump($featuresCountedByPhpVersion['8.0'][FeatureName::UNION_TYPES]);

        die;
    }
}
