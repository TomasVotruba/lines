<?php

declare (strict_types=1);
namespace Lines202512\Termwind\ValueObjects;

use Closure;
use Lines202512\Termwind\Actions\StyleToMethod;
use Lines202512\Termwind\Exceptions\InvalidColor;
/**
 * @internal
 */
final class Style
{
    /**
     * @var Closure(Styles $styles, string|int ...$argument):Styles
     */
    private $callback;
    /**
     * @var string
     */
    private $color = '';
    /**
     * Creates a new value object instance.
     *
     * @param  Closure(Styles $styles, string|int ...$argument): Styles  $callback
     */
    public function __construct(Closure $callback, string $color = '')
    {
        $this->callback = $callback;
        $this->color = $color;
        // ..
    }
    /**
     * Apply the given set of styles to the styles.
     */
    public function apply(string $styles) : void
    {
        $callback = clone $this->callback;
        $this->callback = static function (Styles $formatter, ...$arguments) use($callback, $styles) : Styles {
            $formatter = $callback($formatter, ...$arguments);
            return StyleToMethod::multiple($formatter, $styles);
        };
    }
    /**
     * Sets the color to the style.
     */
    public function color(string $color) : void
    {
        if (\preg_match('/^#([A-Fa-f0-9]{6}|[A-Fa-f0-9]{3})$/', $color) < 1) {
            throw new InvalidColor(\sprintf('The color %s is invalid.', $color));
        }
        $this->color = $color;
    }
    /**
     * Gets the color.
     */
    public function getColor() : string
    {
        return $this->color;
    }
    /**
     * Styles the given formatter with this style.
     * @param string|int ...$arguments
     */
    public function __invoke(Styles $styles, ...$arguments) : Styles
    {
        return ($this->callback)($styles, ...$arguments);
    }
}
