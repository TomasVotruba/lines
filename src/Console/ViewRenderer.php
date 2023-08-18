<?php

declare(strict_types=1);

namespace TomasVotruba\Lines\Console;

use Symfony\Component\Console\Style\SymfonyStyle;
use function Termwind\render;

final class ViewRenderer
{
    public function __construct(
        private readonly SymfonyStyle $symfonyStyle,
    ) {
    }

    public function newLine(): void
    {
        $this->symfonyStyle->writeln('');
    }

    /**
     * @param array<string, mixed> $data
     */
    public function render(string $view, array $data): void
    {
        $viewContent = $this->getViewContent($view, $data);
        render($viewContent);
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
