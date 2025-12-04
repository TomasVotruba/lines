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

        foreach ($featureCollector->getGroupedFeatureCountedByPhpVersion() as $phpVersion => $featureCounts) {
            $this->symfonyStyle->writeln(
                sprintf('<fg=yellow>%s=== PHP ' . $phpVersion . ' ===</>', str_repeat(' ', 24))
            );
            $this->symfonyStyle->newLine();

            $rows = [];
            foreach ($featureCounts as $featureName => $count) {
                $rows[] = [
                    str_pad($featureName, 45, ' ', STR_PAD_RIGHT),
                    str_pad(number_format($count, 0, ',', ' '), 10, ' ', STR_PAD_LEFT)];
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
