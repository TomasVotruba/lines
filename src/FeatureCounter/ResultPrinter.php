<?php

declare(strict_types=1);

namespace TomasVotruba\Lines\FeatureCounter;

use Entropy\Console\Output\OutputPrinter;
use TomasVotruba\Lines\Console\ConsoleTable;
use TomasVotruba\Lines\FeatureCounter\ValueObject\FeatureCollector;

final readonly class ResultPrinter
{
    public function __construct(
        private OutputPrinter $outputPrinter,
        private ConsoleTable $consoleTable,
    ) {
    }

    public function print(FeatureCollector $featureCollector): void
    {
        $this->outputPrinter->title('PHP features');

        $rows = [];

        $previousPhpVersion = null;

        foreach ($featureCollector->getPhpFeatures() as $phpFeature) {
            $changedPhpVersion = $previousPhpVersion !== null && $previousPhpVersion !== $phpFeature->getPhpVersion();
            if ($changedPhpVersion) {
                $rows[] = ConsoleTable::SEPARATOR;
            }

            $rows[] = [
                '<fg=yellow>' . $phpFeature->getPhpVersion() . '</>',
                $phpFeature->getName(),
                str_pad(number_format($phpFeature->getCount(), 0, ',', ' '), 10, ' ', STR_PAD_LEFT),
            ];

            $previousPhpVersion = $phpFeature->getPhpVersion();
        }

        $this->consoleTable->render(['PHP version', 'PHP Feature', 'Count'], $rows);

        $this->outputPrinter->newline();
    }
}
