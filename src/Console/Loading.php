<?php

declare(strict_types=1);

namespace TomasVotruba\Lines\Console;

use Closure;
use Throwable;

final class Loading
{
    /**
     * @var array<int, string>
     */
    private array $frames = [
        '⠄',
        '⠆',
        '⠇',
        '⠋',
        '⠙',
        '⠸',
        '⠰',
        '⠠',
        '⠰',
        '⠸',
        '⠙',
        '⠋',
        '⠇',
        '⠆',
    ];

    public function __construct(
        private readonly View $view
    ) {
    }

    /**
     * @template TReturn of mixed
     *
     * @param Closure(): TReturn $callback
     * @return TReturn
     */
    public function render(string $message, Closure $callback): mixed
    {
        if (! function_exists('pcntl_fork')) {
            $this->view->render('loading', [
                'message' => $message,
            ]);

            return $callback();
        }

        $originalAsync = pcntl_async_signals(true);

        pcntl_signal(SIGINT, function (): never {
            $this->showCursor();
            exit();
        });

        $count = 0;

        try {
            $pid = pcntl_fork();

            if ($pid === 0) {
                while (true) { // @phpstan-ignore-line
                    $this->hideCursor();

                    $this->view->render('loading', [
                        'spinner' => $this->frames[$count++ % count($this->frames)],
                        'message' => $message,
                    ]);

                    usleep(100 * 1000);
                    $this->clear();
                }
            } else {
                $result = $callback();

                posix_kill($pid, SIGHUP);

                $this->clear();
                $this->showCursor();

                pcntl_async_signals($originalAsync);
                pcntl_signal(SIGINT, SIG_DFL);

                return $result;
            }
        } catch (Throwable $throwable) {
            pcntl_async_signals($originalAsync);
            pcntl_signal(SIGINT, SIG_DFL);
            $this->showCursor();

            throw $throwable;
        }
    }

    public function showCursor(): void
    {
        $this->view->write("\e[?25h");
    }

    public function hideCursor(): void
    {
        $this->view->write("\e[?25l");
    }

    public function clear(): void
    {
        $this->view
            ->write("\e[999D") // Move Left
            ->write("\e[3A") // Move Up
            ->write("\e[J"); // Delete
    }
}
