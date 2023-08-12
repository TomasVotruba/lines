<?php

declare(strict_types=1);

namespace TomasVotruba\Lines\Console;

use Closure;
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

    public function write(string $string): self
    {
        $this->output?->write($string);

        return $this;
    }

    /**
     * @template TReturn of mixed
     *
     * @param Closure(): TReturn $callback
     * @return TReturn
     */
    public function loading(string $message, Closure $callback): mixed
    {
        return (new Loading($this))->render($message, $callback);
    }

    /**
     * @param array<string, mixed> $data
     */
    public function render(string $view, array $data): self
    {
        render($this->getViewContent($view, $data));

        return $this;
    }

    /**
     * @param array<string, mixed> $data
     */
    private function getViewContent(string $view, array $data): string
    {
        extract($data);

        ob_start();

        include __DIR__ . sprintf('/views/%s.php', $view);

        $content = (string) ob_get_contents();

        ob_end_clean();

        return $content;
    }
}
