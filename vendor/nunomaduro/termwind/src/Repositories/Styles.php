<?php

declare (strict_types=1);
namespace Lines202412\Termwind\Repositories;

use Closure;
use Lines202412\Termwind\ValueObjects\Style;
use Lines202412\Termwind\ValueObjects\Styles as StylesValueObject;
/**
 * @internal
 */
final class Styles
{
    /**
     * @var array<string, Style>
     */
    private static $storage = [];
    /**
     * Creates a new style from the given arguments.
     *
     * @param  (Closure(StylesValueObject $element, string|int ...$arguments): StylesValueObject)|null  $callback
     */
    public static function create(string $name, ?Closure $callback = null) : Style
    {
        self::$storage[$name] = $style = new Style($callback ?? static function (StylesValueObject $styles) {
            return $styles;
        });
        return $style;
    }
    /**
     * Removes all existing styles.
     */
    public static function flush() : void
    {
        self::$storage = [];
    }
    /**
     * Checks a style with the given name exists.
     */
    public static function has(string $name) : bool
    {
        return \array_key_exists($name, self::$storage);
    }
    /**
     * Gets the style with the given name.
     */
    public static function get(string $name) : Style
    {
        return self::$storage[$name];
    }
}
