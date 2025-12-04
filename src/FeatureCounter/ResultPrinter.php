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

        foreach ($featureCollector->getPhpFeatures() as $phpFeature) {
            $changedPhpVersion = $previousPhpVersion !== null && $previousPhpVersion !== $phpFeature->getPhpVersion();
            if ($changedPhpVersion) {
                // add empty breakline
                $rows[] = new TableSeparator();
            }

            $rows[] = [
                '<fg=yellow>' . $phpFeature->getPhpVersion() . '</>',
                str_pad($phpFeature->getName(), 45, ' ', STR_PAD_RIGHT),
                str_pad(number_format($phpFeature->getCount(), 0, ',', ' '), 10, ' ', STR_PAD_LEFT)];

            $previousPhpVersion = $phpFeature->getPhpVersion();

        }

        $this->symfonyStyle->table(['PHP version', 'Feature count'], $rows);

        $this->symfonyStyle->newLine();
    }
}
