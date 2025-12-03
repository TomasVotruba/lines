<?php

declare(strict_types=1);

namespace Rector\FeatureCounter;

use Rector\FeatureCounter\ValueObject\FeatureCollector;
use Symfony\Component\Console\Helper\Table;
use Symfony\Component\Console\Output\ConsoleOutput;
use Symfony\Component\Console\Style\SymfonyStyle;

final class ResultPrinter
{
    public function print(FeatureCollector $featureCollector, SymfonyStyle $symfonyStyle): void
    {
        $symfonyStyle->newLine();

        foreach ($featureCollector->getGroupedFeatureCountedByPhpVersion() as $phpVersion => $featureCounts) {
            $symfonyStyle->writeln(sprintf('<fg=yellow>%s*** PHP ' . $phpVersion . ' ***</>', str_repeat(' ', 34)));
            $symfonyStyle->newLine();

            $rows = [];
            foreach ($featureCounts as $featureName => $count) {
                $rows[] = [
                    str_pad($featureName, 65, ' ', STR_PAD_RIGHT),
                    str_pad((string) number_format($count, 0, ',', ' '), 10, ' ', STR_PAD_LEFT)];
            }

            // Create a new Table instance
            $table = new Table(new ConsoleOutput());
            $table->setHeaders(['Feature', 'Count']);
            $table->setRows($rows);

            $symfonyStyle->table(['Feature', 'Count'], $rows);
        }

        $symfonyStyle->newLine();
    }
}
