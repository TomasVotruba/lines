<?php

declare (strict_types=1);
namespace Lines202401\Termwind;

use Closure;
use Lines202401\Symfony\Component\Console\Output\OutputInterface;
use Lines202401\Termwind\Repositories\Styles as StyleRepository;
use Lines202401\Termwind\ValueObjects\Style;
use Lines202401\Termwind\ValueObjects\Styles;
if (!\function_exists('Lines202401\\Termwind\\renderUsing')) {
    /**
     * Sets the renderer implementation.
     */
    function renderUsing(?\Lines202401\Symfony\Component\Console\Output\OutputInterface $renderer) : void
    {
        Termwind::renderUsing($renderer);
    }
}
if (!\function_exists('Lines202401\\Termwind\\style')) {
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
if (!\function_exists('Lines202401\\Termwind\\render')) {
    /**
     * Render HTML to a string.
     */
    function render(string $html, int $options = OutputInterface::OUTPUT_NORMAL) : void
    {
        (new HtmlRenderer())->render($html, $options);
    }
}
if (!\function_exists('Lines202401\\Termwind\\terminal')) {
    /**
     * Returns a Terminal instance.
     */
    function terminal() : Terminal
    {
        return new Terminal();
    }
}
if (!\function_exists('Lines202401\\Termwind\\ask')) {
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
