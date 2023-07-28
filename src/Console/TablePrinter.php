<?php

declare(strict_types=1);

namespace TomasVotruba\Lines\Console;

use Symfony\Component\Console\Helper\TableStyle;
use Symfony\Component\Console\Style\SymfonyStyle;
use TomasVotruba\Lines\Helpers\NumberFormat;

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
     * @param array<array{0: string, 1: int, 2?: float}> $rows
     * @return array<array{0: string, 1: int|string, 2?: float|string}>
     */
    private function formatRowsNumbers(array $rows): array
    {
        foreach ($rows as $key => $row) {
            // big numbers
            if ($key === 1) {
                $rows[$key][1] = NumberFormat::pretty($row[1]);
                continue;
            }

            // float relatives
            if (isset($row[2]) && $key === 2) {
                $rows[$key][2] = NumberFormat::percent($row[2]);
            }
        }

        return $rows;
    }
}
