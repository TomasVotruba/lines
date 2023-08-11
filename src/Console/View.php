<?php

declare(strict_types=1);

namespace TomasVotruba\Lines\Console;

use Symfony\Component\Console\Output\OutputInterface;
use function Termwind\render;
use function Termwind\renderUsing;

final class View
{
    private ?OutputInterface $output = null;

    public function setOutput(OutputInterface $output): self
    {
        renderUsing($this->output = $output);

        return $this;
    }

    public function newLine(): self
    {
        $this->output?->writeln('');

        return $this;
    }

    /**
     * @param array<string, mixed> $data
     */
    public function render(string $view, array $data): self
    {
        extract($data);

        ob_start();

        include __DIR__ . sprintf('/views/%s.php', $view);

        render((string) ob_get_contents());

        ob_end_clean();

        return $this;
    }
}
