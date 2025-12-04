<?php

declare(strict_types=1);

namespace TomasVotruba\Lines\Tests\FeatureCounter\Analyzer;

use PHPUnit\Framework\TestCase;
use TomasVotruba\Lines\DependencyInjection\ContainerFactory;
use TomasVotruba\Lines\FeatureCounter\Analyzer\FeatureCounterAnalyzer;
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

        $featuresCountedByPhpVersion = $featureCollector->getFeaturesGroupedByPhpVersion();

        $typedPropertiesPhpFeature = $featuresCountedByPhpVersion['7.4'][0];
        $this->assertSame('Typed properties', $typedPropertiesPhpFeature->getName());
        $this->assertSame(1, $typedPropertiesPhpFeature->getCount());

        $unionTypedPhpFeature = $featuresCountedByPhpVersion['8.0'][1];
        $this->assertSame('Union types', $unionTypedPhpFeature->getName());
        $this->assertSame(2, $unionTypedPhpFeature->getCount());
    }
}
