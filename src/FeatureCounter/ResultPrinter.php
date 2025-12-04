<?php

declare(strict_types=1);

namespace TomasVotruba\Lines\FeatureCounter;

use Symfony\Component\Console\Helper\Table;
use Symfony\Component\Console\Output\ConsoleOutput;
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
        $this->symfonyStyle->newLine();

        foreach ($featureCollector->getGroupedFeatureCountedByPhpVersion() as $phpVersion => $featureCounts) {
            $this->symfonyStyle->writeln(
                sprintf('<fg=yellow>%s*** PHP ' . $phpVersion . ' ***</>', str_repeat(' ', 34))
            );
            $this->symfonyStyle->newLine();

            $rows = [];
            foreach ($featureCounts as $featureName => $count) {
                $rows[] = [
                    str_pad($featureName, 65, ' ', STR_PAD_RIGHT),
                    str_pad(number_format($count, 0, ',', ' '), 10, ' ', STR_PAD_LEFT)];
            }

            // Create a new Table instance
            $table = new Table(new ConsoleOutput());
            $table->setHeaders(['Feature', 'Count']);
            $table->setRows($rows);

            $this->symfonyStyle->table(['Feature', 'Count'], $rows);
        }

        $this->symfonyStyle->newLine();
    }
}
