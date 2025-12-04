<?php

declare(strict_types=1);

namespace TomasVotruba\Lines\FeatureCounter\ValueObject;

final class PhpFeature
{
    /**
     * @param callable $nodeTrigger
     */
    public function __construct(
        private readonly int $phpVersion,
        private readonly string $name,
        private $nodeTrigger,
        private int $count = 0
    ) {
    }

    public function getPhpVersion(): int
    {
        return $this->phpVersion;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getNodeTrigger(): callable
    {
        return $this->nodeTrigger;
    }

    public function increaseCount(): void
    {
        $this->count++;
    }

    public function getCount(): int
    {
        return $this->count;
    }
}
