<?php

declare(strict_types=1);

namespace TomasVotruba\Lines\ValueObject;

/**
 * @api used in templates
 */
final class TableView
{
    /**
     * @param array<array{}|array{name: string, count: int|float|string, percent: float|string|null, isChild: bool}> $rows
     */
    public function __construct(
        private readonly string $title,
        private readonly string $label,
        private readonly array $rows,
        private readonly bool $shouldIncludeRelative = false,
    ) {
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
     * @return array<array{}|array{name: string, count: int|float|string, percent: float|string|null, isChild: bool}>
     */
    public function getRows(): array
    {
        return $this->rows;
    }

    public function getTemplateFilePath(): string
    {
        return __DIR__ . '/../../views/table.php';
    }
}
