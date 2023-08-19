<?php

declare(strict_types=1);

namespace TomasVotruba\Lines\ValueObject;

/**
 * @api used in templates
 */
final class TableRow
{
    public function __construct(
        private readonly string $name,
        private readonly string $count,
        private readonly ?string $percent,
        private readonly bool $isChild,
    ) {
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getCount(): string
    {
        return $this->count;
    }

    public function getPercent(): ?string
    {
        return $this->percent;
    }

    public function isChild(): bool
    {
        return $this->isChild;
    }
}
