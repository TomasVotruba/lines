<?php

declare(strict_types=1);

namespace TomasVotruba\Lines\CLI;

final class Arguments
{
    /**
     * @param string[] $directories
     * @param string[] $suffixes
     * @param string[] $exclude
     */
    public function __construct(
        private readonly array $directories,
        private readonly array $suffixes,
        private readonly array $exclude,
        private readonly ?string $jsonLogfile,
        private readonly bool $help,
    ) {
    }

    /**
     * @return string[]
     */
    public function directories(): array
    {
        return $this->directories;
    }

    /**
     * @return string[]
     */
    public function suffixes(): array
    {
        return $this->suffixes;
    }

    /**
     * @return string[]
     */
    public function exclude(): array
    {
        return $this->exclude;
    }

    public function jsonLogfile(): ?string
    {
        return $this->jsonLogfile;
    }

    public function help(): bool
    {
        return $this->help;
    }
}
