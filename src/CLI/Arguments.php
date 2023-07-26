<?php declare(strict_types=1);
namespace TomasVotruba\Lines;

final class Arguments
{
    /**
     * @psalm-var list<string>
     */
    private array $directories;

    /**
     * @psalm-var list<string>
     */
    private array $suffixes;

    /**
     * @psalm-var list<string>
     */
    private array $exclude;

    private bool $countTests;

    private ?string $jsonLogfile = null;

    private bool $help;

    private bool $version;

    public function __construct(array $directories, array $suffixes, array $exclude, bool $countTests, ?string $jsonLogfile, bool $help, bool $version)
    {
        $this->directories = $directories;
        $this->suffixes    = $suffixes;
        $this->exclude     = $exclude;
        $this->countTests  = $countTests;
        $this->jsonLogfile = $jsonLogfile;
        $this->help        = $help;
        $this->version     = $version;
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
