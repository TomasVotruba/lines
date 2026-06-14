<?php

declare(strict_types=1);

namespace TomasVotruba\Lines\Console;

use Entropy\Console\Enum\Color;
use Entropy\Console\Output\OutputColorizer;
use Entropy\Console\Output\OutputPrinter;
use TomasVotruba\Lines\ValueObject\TableRow;
use TomasVotruba\Lines\ValueObject\TableView;

final readonly class ViewRenderer
{
    private const WIDTH = 60;

    public function __construct(
        private OutputPrinter $outputPrinter,
        private OutputColorizer $outputColorizer,
    ) {
    }

    public function renderTableView(TableView $tableView): void
    {
        $this->outputPrinter->newline();
        $this->renderHeader($tableView);

        foreach ($tableView->getRows() as $tableRow) {
            $this->renderRow($tableRow);
        }
    }

    private function renderHeader(TableView $tableView): void
    {
        $title = $this->outputColorizer->color($tableView->getTitle(), Color::GREEN);

        $label = strtolower($tableView->getLabel());
        if ($tableView->isShouldIncludeRelative()) {
            $label .= ' / Relative';
        }

        $this->outputPrinter->writeln($this->createLeaderLine($title, $label, ' '));
    }

    private function renderRow(TableRow $tableRow): void
    {
        $name = $tableRow->isChild() ? '  ' . $tableRow->getName() : $tableRow->getName();

        $value = $tableRow->getCount();
        if ($tableRow->getPercent() !== null) {
            $value .= ' / ' . $tableRow->getPercent();
        }

        $this->outputPrinter->writeln($this->createLeaderLine($name, $value, '.'));
    }

    /**
     * Renders "left .......... right" aligned to a fixed width, ignoring color tags.
     */
    private function createLeaderLine(string $left, string $right, string $fillChar): string
    {
        $leaderCount = self::WIDTH - $this->visibleLength($left) - $this->visibleLength($right) - 2;
        $leaderCount = max(1, $leaderCount);

        return $left . ' ' . str_repeat($fillChar, $leaderCount) . ' ' . $right;
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
