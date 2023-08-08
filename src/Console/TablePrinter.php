<?php

declare(strict_types=1);

namespace TomasVotruba\Lines\Console;

use TomasVotruba\Lines\Helpers\NumberFormat;
use function Termwind\render;

final class TablePrinter
{
    /**
     * @param array<array{0?: string, 1?: int|float, 2?: float|null, 3?: bool}> $rows
     */
    public function printItemValueTable(
        array $rows,
        string $titleHeader,
        string $countHeader,
        bool $includeRelative = false
    ): void {
        $relative = $includeRelative ? <<<HTML
            <span class="text-gray mr-1">/</span>
            <span class="text-gray font-bold">Relative</span>
        HTML : '';

        $rows = implode('', array_map(
            fn (array $row): string => $this->renderRow($row),
            $this->formatRowsNumbers($rows)
        ));

        render(<<<HTML
            <div class="mt-1 mx-2 max-w-70">
                <div class="flex justify-between">
                    <span class="text-green font-bold">{$titleHeader}</span>
                    <span class="lowercase">{$countHeader} {$relative}</span>
                </div>
                {$rows}
            </div>
        HTML);
    }

    /**
     * @param array{0?: string, 1?: int|float|string, 2?: float|string|null, 3?: bool} $row
     */
    public function renderRow(array $row): string
    {
        if ($row === []) {
            return '<div />';
        }

        $relative = isset($row[2]) ? <<<HTML
            <span>
                <span class="text-gray mr-1">/</span>
                <span class="text-gray w-6 text-right">{$row[2]}</span>
            </span>
        HTML : '';

        $isChildClasses = isset($row[3]) ? 'ml-1' : '';

        return <<<HTML
            <div class="flex space-x-1">
                <span class="{$isChildClasses}">{$row[0]}</span>
                <span class="flex-1 content-repeat-[.] text-gray"></span>
                <span>{$row[1]}</span>
                {$relative}
            </div>
        HTML;
    }

    /**
     * @param array<array{0?: string, 1?: int|float, 2?: float|null, 3?: bool}> $rows
     * @return array<array{0?: string, 1?: int|float|string, 2?: float|string|null, 3?: bool}>
     */
    private function formatRowsNumbers(array $rows): array
    {
        foreach ($rows as $key => $row) {
            if ($row === []) {
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
