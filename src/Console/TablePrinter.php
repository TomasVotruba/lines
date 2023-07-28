<?php

declare(strict_types=1);

namespace TomasVotruba\Lines\Console;

use Symfony\Component\Console\Helper\TableSeparator;
use Symfony\Component\Console\Helper\TableStyle;
use Symfony\Component\Console\Style\SymfonyStyle;
use TomasVotruba\Lines\Helpers\NumberFormat;
use Webmozart\Assert\Assert;

final class TablePrinter
{
    private readonly TableStyle $padLeftTableStyle;

    public function __construct(
        private readonly SymfonyStyle $symfonyStyle
    ) {
        $padLeftTableStyle = new TableStyle();
        $padLeftTableStyle->setPadType(STR_PAD_LEFT);

        $this->padLeftTableStyle = $padLeftTableStyle;
    }

    /**
     * @param mixed[] $rows
     */
    public function printItemValueTable(
        array $rows,
        string $titleHeader,
        string $countHeader,
        bool $includeRelative = false
    ): void {
        Assert::allIsArray($rows);

        $headers = [$titleHeader, $countHeader];
        if ($includeRelative) {
            $headers[] = 'Relative';
        }

        $formattedRows = $this->formatRowsNumbers($rows);

        $table = $this->symfonyStyle->createTable()
            ->setHeaders($headers)
            ->setColumnWidth(0, 28)
            ->setRows($formattedRows)
            ->setColumnStyle(1, $this->padLeftTableStyle);

        if ($includeRelative) {
            $table->setColumnWidth(1, 8)
                ->setColumnWidth(2, 7)
                ->setColumnStyle(2, $this->padLeftTableStyle);
        } else {
            $table->setColumnWidth(1, 19);
        }

        $table->render();

        $this->symfonyStyle->newLine();
    }

    /**
     * @param array<array{0: string|TableSeparator, 1: int|TableSeparator, 2?: float|TableSeparator}> $rows
     * @return array<array{0: string|TableSeparator, 1: int|string|TableSeparator, 2?: float|string|TableSeparator}>
     */
    private function formatRowsNumbers(array $rows): array
    {
        foreach ($rows as $key => $row) {
            // big numbers
            if ($key === 1) {

                // keep separator
                if ($row[1] instanceof TableSeparator) {
                    continue;
                }

                $rows[$key][1] = NumberFormat::pretty($row[1]);
                continue;
            }

            // float relatives
            if (isset($row[2]) && $key === 2) {
                // keep separator
                if ($row[2] instanceof TableSeparator) {
                    continue;
                }

                $rows[$key][2] = NumberFormat::percent($row[2]);
            }
        }

        return $rows;
    }
}
