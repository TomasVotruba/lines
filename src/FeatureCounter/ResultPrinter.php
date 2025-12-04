<?php

declare(strict_types=1);

namespace TomasVotruba\Lines\FeatureCounter;

use Symfony\Component\Console\Style\SymfonyStyle;
use TomasVotruba\Lines\FeatureCounter\ValueObject\FeatureCollector;

final readonly class ResultPrinter
{
    public function __construct(
        private SymfonyStyle $symfonyStyle
    ) {

    }

    public function print(FeatureCollector $featureCollector): void
    {
        $this->symfonyStyle->newLine(2);

        foreach ($featureCollector->getFeaturesGroupedByPhpVersion() as $phpVersion => $phpFeatures) {
            $this->symfonyStyle->writeln(
                sprintf('<fg=yellow>%s=== PHP ' . $phpVersion . ' ===</>', str_repeat(' ', 24))
            );

            $this->symfonyStyle->newLine();

            $rows = [];
            foreach ($phpFeatures as $phpFeature) {
                $rows[] = [
                    str_pad($phpFeature->getName(), 45, ' ', STR_PAD_RIGHT),
                    str_pad(number_format($phpFeature->getCount(), 0, ',', ' '), 10, ' ', STR_PAD_LEFT)];
            }

            $this->symfonyStyle->table(['Feature', 'Count'], $rows);
        }

        $this->symfonyStyle->newLine();

        $this->symfonyStyle->writeln(
            sprintf('<fg=yellow>%s=== Summary by PHP version ===</>', str_repeat(' ', 16))
        );
        $this->symfonyStyle->newLine();

        $rows = [];
        foreach ($featureCollector->getFeatureCountByPhpVersion() as $phpVersion => $featureCount) {
            $rows[] = [
                str_pad($phpVersion, 40, ' ', STR_PAD_RIGHT),
                str_pad(number_format($featureCount, 0, ',', ' '), 15, ' ', STR_PAD_LEFT)];
        }

        $this->symfonyStyle->table(['PHP version', 'Feature count'], $rows);

        $this->symfonyStyle->newLine();
    }
}
