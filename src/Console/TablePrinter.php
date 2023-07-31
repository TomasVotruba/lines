<?php

declare (strict_types=1);
namespace Lines202307\TomasVotruba\Lines\Console;

use Lines202307\Symfony\Component\Console\Helper\TableSeparator;
use Lines202307\Symfony\Component\Console\Helper\TableStyle;
use Lines202307\Symfony\Component\Console\Style\SymfonyStyle;
use Lines202307\TomasVotruba\Lines\Helpers\NumberFormat;
final class TablePrinter
{
    /**
     * @readonly
     * @var \Symfony\Component\Console\Style\SymfonyStyle
     */
    private $symfonyStyle;
    /**
     * @readonly
     * @var \Symfony\Component\Console\Helper\TableStyle
     */
    private $padLeftTableStyle;
    public function __construct(SymfonyStyle $symfonyStyle)
    {
        $this->symfonyStyle = $symfonyStyle;
        $padLeftTableStyle = new TableStyle();
        $padLeftTableStyle->setPadType(\STR_PAD_LEFT);
        $this->padLeftTableStyle = $padLeftTableStyle;
    }
    /**
     * @param array<array{0: string, 1: int|float, 2?: float}|TableSeparator> $rows
     */
    public function printItemValueTable(array $rows, string $titleHeader, string $countHeader, bool $includeRelative = \false) : void
    {
        $headers = [$titleHeader, $countHeader];
        if ($includeRelative) {
            $headers[] = 'Relative';
        }
        $formattedRows = $this->formatRowsNumbers($rows);
        $table = $this->symfonyStyle->createTable()->setHeaders($headers)->setColumnWidth(0, 28)->setRows($formattedRows)->setColumnStyle(1, $this->padLeftTableStyle);
        if ($includeRelative) {
            $table->setColumnWidth(1, 8)->setColumnWidth(2, 7)->setColumnStyle(2, $this->padLeftTableStyle);
        } else {
            $table->setColumnWidth(1, 19);
        }
        $table->render();
        $this->symfonyStyle->newLine();
    }
    /**
     * @param array<TableSeparator|array{0: string, 1: int|float, 2?: float}> $rows
     * @return array<TableSeparator|array{0: string, 1: int|float|string, 2?: float|string}>
     */
    private function formatRowsNumbers(array $rows) : array
    {
        foreach ($rows as $key => $row) {
            if ($row instanceof TableSeparator) {
                continue;
            }
            // make big numbers separated with space, e.g. "1 234"
            $rows[$key][1] = NumberFormat::pretty($row[1]);
            // optional float relatives
            if (isset($row[2])) {
                $rows[$key][2] = NumberFormat::percent($row[2]);
            }
        }
        return $rows;
    }
}
