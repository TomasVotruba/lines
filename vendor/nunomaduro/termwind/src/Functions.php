<?php

declare (strict_types=1);
namespace Lines202402\Termwind;

use Closure;
use Lines202402\Symfony\Component\Console\Output\OutputInterface;
use Lines202402\Termwind\Repositories\Styles as StyleRepository;
use Lines202402\Termwind\ValueObjects\Style;
use Lines202402\Termwind\ValueObjects\Styles;
if (!\function_exists('Lines202402\\Termwind\\renderUsing')) {
    /**
     * Sets the renderer implementation.
     */
    function renderUsing(?\Lines202402\Symfony\Component\Console\Output\OutputInterface $renderer) : void
    {
        Termwind::renderUsing($renderer);
    }
}
if (!\function_exists('Lines202402\\Termwind\\style')) {
    /**
     * Creates a new style.
     *
     * @param (Closure(Styles $renderable, string|int ...$arguments): Styles)|null $callback
     */
    function style(string $name, Closure $callback = null) : Style
    {
        return StyleRepository::create($name, $callback);
    }
}
if (!\function_exists('Lines202402\\Termwind\\render')) {
    /**
     * Render HTML to a string.
     */
    function render(string $html, int $options = OutputInterface::OUTPUT_NORMAL) : void
    {
        (new HtmlRenderer())->render($html, $options);
    }
}
if (!\function_exists('Lines202402\\Termwind\\terminal')) {
    /**
     * Returns a Terminal instance.
     */
    function terminal() : Terminal
    {
        return new Terminal();
    }
}
if (!\function_exists('Lines202402\\Termwind\\ask')) {
    /**
     * Renders a prompt to the user.
     *
     * @param  iterable<array-key, string>|null  $autocomplete
     * @return mixed
     */
    function ask(string $question, iterable $autocomplete = null)
    {
        return (new Question())->ask($question, $autocomplete);
    }
}
