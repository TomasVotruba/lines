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
        $this->renderView('table', [
            'includeRelative' => $includeRelative,
            'titleHeader' => $titleHeader,
            'countHeader' => $countHeader,
            'rows' => $this->formatRowsNumbers($rows),
        ]);
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

    /**
     * @param array<string, mixed> $data
     */
    private function renderView(string $view, array $data): void
    {
        extract($data);

        ob_start();

        include __DIR__ . sprintf('/views/%s.php', $view);

        render((string) ob_get_contents());

        ob_end_clean();
    }
}
