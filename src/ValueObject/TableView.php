<?php

declare(strict_types=1);

namespace TomasVotruba\Lines\ValueObject;

use Webmozart\Assert\Assert;

final readonly class TableView
{
    /**
     * @param TableRow[] $tableRows
     */
    public function __construct(
        private string $title,
        private string $label,
        private array $tableRows,
        private bool $shouldIncludeRelative = false,
    ) {
        Assert::allIsInstanceOf($tableRows, TableRow::class);
    }

    public function getTitle(): string
    {
        return $this->title;
    }

    public function getLabel(): string
    {
        return $this->label;
    }

    public function isShouldIncludeRelative(): bool
    {
        return $this->shouldIncludeRelative;
    }

    /**
     * @return TableRow[]
     */
    public function getRows(): array
    {
        return $this->tableRows;
    }
}
