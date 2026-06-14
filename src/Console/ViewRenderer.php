<?php

declare(strict_types=1);

namespace TomasVotruba\Lines\Console;

use Entropy\Console\Output\OutputPrinter;
use TomasVotruba\Lines\ValueObject\TableRow;
use TomasVotruba\Lines\ValueObject\TableView;

final readonly class ViewRenderer
{
    public function __construct(
        private OutputPrinter $outputPrinter,
        private ConsoleTable $consoleTable,
    ) {
    }

    public function renderTableView(TableView $tableView): void
    {
        $this->outputPrinter->newline();

        $headers = [$tableView->getTitle(), $tableView->getLabel()];
        if ($tableView->isShouldIncludeRelative()) {
            $headers[] = 'Relative';
        }

        $countColumnWidth = $this->resolveCountColumnWidth($tableView);
        $percentColumnWidth = $this->resolvePercentColumnWidth($tableView);

        $rows = [];
        foreach ($tableView->getRows() as $tableRow) {
            $rows[] = $this->createRow($tableRow, $tableView, $countColumnWidth, $percentColumnWidth);
        }

        $this->consoleTable->render($headers, $rows);
    }

    /**
     * @return string[]
     */
    private function createRow(
        TableRow $tableRow,
        TableView $tableView,
        int $countColumnWidth,
        int $percentColumnWidth
    ): array {
        $name = $tableRow->isChild() ? '  ' . $tableRow->getName() : $tableRow->getName();

        $row = [$name, str_pad($tableRow->getCount(), $countColumnWidth, ' ', STR_PAD_LEFT)];

        if ($tableView->isShouldIncludeRelative()) {
            $row[] = str_pad((string) $tableRow->getPercent(), $percentColumnWidth, ' ', STR_PAD_LEFT);
        }

        return $row;
    }

    private function resolveCountColumnWidth(TableView $tableView): int
    {
        $width = strlen($tableView->getLabel());
        foreach ($tableView->getRows() as $tableRow) {
            $width = max($width, strlen($tableRow->getCount()));
        }

        return $width;
    }

    private function resolvePercentColumnWidth(TableView $tableView): int
    {
        $width = strlen('Relative');
        foreach ($tableView->getRows() as $tableRow) {
            if ($tableRow->getPercent() !== null) {
                $width = max($width, strlen($tableRow->getPercent()));
            }
        }

        return $width;
    }
}
