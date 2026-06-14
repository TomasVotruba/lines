<?php

declare(strict_types=1);

namespace TomasVotruba\Lines\Console;

use Entropy\Console\Enum\Color;
use Entropy\Console\Output\OutputColorizer;
use Entropy\Console\Output\OutputPrinter;

/**
 * @see \TomasVotruba\Lines\Tests\Console\ConsoleTableTest
 */
final readonly class ConsoleTable
{
    /**
     * Marks a separator line between table rows.
     *
     * @var string
     */
    public const SEPARATOR = '__separator__';

    private const COLUMN_PADDING = 2;

    public function __construct(
        private OutputPrinter $outputPrinter,
        private OutputColorizer $outputColorizer,
    ) {
    }

    /**
     * @param string[] $headers
     * @param array<string[]|self::SEPARATOR> $rows
     */
    public function render(array $headers, array $rows): void
    {
        $columnWidths = $this->resolveColumnWidths($headers, $rows);

        $coloredHeaders = array_map(
            fn (string $header): string => $this->outputColorizer->color($header, Color::YELLOW),
            $headers,
        );

        $this->outputPrinter->writeln($this->formatRow($coloredHeaders, $columnWidths));
        $this->outputPrinter->writeln($this->createSeparatorLine($columnWidths));

        foreach ($rows as $row) {
            if ($row === self::SEPARATOR) {
                $this->outputPrinter->writeln($this->createSeparatorLine($columnWidths));
                continue;
            }

            $this->outputPrinter->writeln($this->formatRow($row, $columnWidths));
        }
    }

    /**
     * @param string[] $headers
     * @param array<string[]|self::SEPARATOR> $rows
     * @return int[]
     */
    private function resolveColumnWidths(array $headers, array $rows): array
    {
        $columnWidths = [];
        foreach ($headers as $columnIndex => $header) {
            $columnWidths[$columnIndex] = $this->visibleLength($header);
        }

        foreach ($rows as $row) {
            if ($row === self::SEPARATOR) {
                continue;
            }

            foreach ($row as $columnIndex => $cell) {
                $columnWidths[$columnIndex] = max($columnWidths[$columnIndex] ?? 0, $this->visibleLength($cell));
            }
        }

        return $columnWidths;
    }

    /**
     * @param string[] $cells
     * @param int[] $columnWidths
     */
    private function formatRow(array $cells, array $columnWidths): string
    {
        $paddedCells = [];
        foreach ($cells as $columnIndex => $cell) {
            $padding = $columnWidths[$columnIndex] - $this->visibleLength($cell) + self::COLUMN_PADDING;
            $paddedCells[] = $cell . str_repeat(' ', $padding);
        }

        return rtrim(implode('', $paddedCells));
    }

    /**
     * @param int[] $columnWidths
     */
    private function createSeparatorLine(array $columnWidths): string
    {
        $totalWidth = array_sum($columnWidths) + (count($columnWidths) * self::COLUMN_PADDING);

        return str_repeat('-', $totalWidth);
    }

    /**
     * Length of the text as rendered, ignoring color tags like "<fg=green>".
     */
    private function visibleLength(string $text): int
    {
        $stripped = preg_replace('#</?>|<(?:fg|bg)=(?:green|yellow|red|cyan|orange|grey)>#', '', $text);

        return strlen($stripped ?? $text);
    }
}
