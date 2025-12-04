<?php

declare(strict_types=1);

namespace TomasVotruba\Lines\FeatureCounter;

use Symfony\Component\Console\Helper\TableSeparator;
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

        $rows = [];

        $previousPhpVersion = null;
        $lastItemKey = array_key_last($featureCollector->getPhpFeatures());
        foreach ($featureCollector->getPhpFeatures() as $key => $phpFeature) {

            //            foreach ($phpFeatures as $phpFeature) {
            $rows[] = [
                '<fg=yellow>' . $phpFeature->getPhpVersion() . '</>',
                str_pad($phpFeature->getName(), 45, ' ', STR_PAD_RIGHT),
                str_pad(number_format($phpFeature->getCount(), 0, ',', ' '), 10, ' ', STR_PAD_LEFT)];
            //            }

            $changedPhpVersion = $previousPhpVersion !== null && $previousPhpVersion !== $phpFeature->getPhpVersion();
            $previousPhpVersion = $phpFeature->getPhpVersion();

            if ($changedPhpVersion && $lastItemKey !== $key) {
                // add empty breakline
                $rows[] = new TableSeparator();
            }
        }

        $this->symfonyStyle->table(['PHP', 'Feature', 'Count'], $rows);

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
