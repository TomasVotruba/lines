<?php declare(strict_types=1);
namespace TomasVotruba\Lines;

final class Arguments
{
    public function __construct(
        /**
         * @psalm-var list<string>
         */
        private readonly array $directories,
        /**
         * @psalm-var list<string>
         */
        private readonly array $suffixes,
        /**
         * @psalm-var list<string>
         */
        private readonly array $exclude,
        private readonly bool $countTests,
        private readonly ?string $jsonLogfile,
        private readonly bool $help,
        private readonly bool $version
    )
    {
    }

    /**
     * @psalm-return list<string>
     */
    public function directories(): array
    {
        return $this->directories;
    }

    /**
     * @psalm-return list<string>
     */
    public function suffixes(): array
    {
        return $this->suffixes;
    }

    /**
     * @psalm-return list<string>
     */
    public function exclude(): array
    {
        return $this->exclude;
    }

    public function countTests(): bool
    {
        return $this->countTests;
    }

    public function jsonLogfile(): ?string
    {
        return $this->jsonLogfile;
    }

    public function help(): bool
    {
        return $this->help;
    }

    public function version(): bool
    {
        return $this->version;
    }
}
