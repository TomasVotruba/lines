<?php

declare(strict_types=1);

namespace TomasVotruba\Lines\ValueObject;

use Webmozart\Assert\Assert;

/**
 * @api used in templates
 */
final class TableView
{
    /**
     * @param TableRow[] $tableRows
     */
    public function __construct(
        private readonly string $title,
        private readonly string $label,
        private readonly array $tableRows,
        private readonly bool $shouldIncludeRelative = false,
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

    public function getTemplateFilePath(): string
    {
        return __DIR__ . '/../../views/table.php';
    }
}
