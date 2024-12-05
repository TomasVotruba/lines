<?php

declare (strict_types=1);
namespace Lines202412\Termwind\ValueObjects;

use Closure;
use Lines202412\Termwind\Actions\StyleToMethod;
use Lines202412\Termwind\Components\Element;
use Lines202412\Termwind\Components\Hr;
use Lines202412\Termwind\Components\Li;
use Lines202412\Termwind\Components\Ol;
use Lines202412\Termwind\Components\Ul;
use Lines202412\Termwind\Enums\Color;
use Lines202412\Termwind\Exceptions\ColorNotFound;
use Lines202412\Termwind\Exceptions\InvalidStyle;
use Lines202412\Termwind\Repositories\Styles as StyleRepository;
use function Lines202412\Termwind\terminal;
/**
 * @internal
 */
final class Styles
{
    /**
     * @var array<string, mixed>
     */
    private $properties = ['colors' => [], 'options' => [], 'isFirstChild' => \false];
    /**
     * @var array<string, Closure(string, array<string, (string | int)>, array<string, int[]>):string>
     */
    private $textModifiers = [];
    /**
     * @var array<string, Closure(string, array<string, (string | int)>):string>
     */
    private $styleModifiers = [];
    /**
     * @var string[]
     */
    private $defaultStyles = [];
    /**
     * Finds all the styling on a string.
     */
    public const STYLING_REGEX = "/\\<[\\w=#\\/\\;,:.&,%?-]+\\>|\\e\\[\\d+m/";
    /** @var array<int, string> */
    private $styles = [];
    /**
     * @var \Termwind\Components\Element|null
     */
    private $element;
    /**
     * Creates a Style formatter instance.
     *
     * @param  array<string, mixed>  $properties
     * @param  array<string, Closure(string, array<string, string|int>, array<string, int[]>): string>  $textModifiers
     * @param  array<string, Closure(string, array<string, string|int>): string>  $styleModifiers
     * @param  string[]  $defaultStyles
     */
    public final function __construct(array $properties = ['colors' => [], 'options' => [], 'isFirstChild' => \false], array $textModifiers = [], array $styleModifiers = [], array $defaultStyles = [])
    {
        $this->properties = $properties;
        $this->textModifiers = $textModifiers;
        $this->styleModifiers = $styleModifiers;
        $this->defaultStyles = $defaultStyles;
    }
    /**
     * @return $this
     */
    public function setElement(Element $element) : self
    {
        $this->element = $element;
        return $this;
    }
    /**
     * Gets default styles.
     *
     * @return string[]
     */
    public function defaultStyles() : array
    {
        return $this->defaultStyles;
    }
    /**
     * Gets the element's style properties.
     *
     * @return array<string, mixed>
     */
    public final function getProperties() : array
    {
        return $this->properties;
    }
    /**
     * Sets the element's style properties.
     *
     * @param  array<string, mixed>  $properties
     */
    public function setProperties(array $properties) : self
    {
        $this->properties = $properties;
        return $this;
    }
    /**
     * Sets the styles to the element.
     */
    public final function setStyle(string $style) : self
    {
        $this->styles = \array_unique(\array_merge($this->styles, [$style]));
        return $this;
    }
    /**
     * Checks if the element has the style.
     */
    public final function hasStyle(string $style) : bool
    {
        return \in_array($style, $this->styles, \true);
    }
    /**
     * Adds a style to the element.
     */
    public final function addStyle(string $style) : self
    {
        return StyleToMethod::multiple($this, $style);
    }
    /**
     * Inherit styles from given Styles object.
     */
    public final function inheritFromStyles(self $styles) : self
    {
        foreach (['ml', 'mr', 'pl', 'pr', 'width', 'minWidth', 'maxWidth', 'spaceY', 'spaceX'] as $style) {
            $this->properties['parentStyles'][$style] = \array_merge($this->properties['parentStyles'][$style] ?? [], $styles->properties['parentStyles'][$style] ?? []);
            $this->properties['parentStyles'][$style][] = $styles->properties['styles'][$style] ?? 0;
        }
        $this->properties['parentStyles']['justifyContent'] = $styles->properties['styles']['justifyContent'] ?? \false;
        foreach (['bg', 'fg'] as $colorType) {
            $value = (array) ($this->properties['colors'][$colorType] ?? []);
            $parentValue = (array) ($styles->properties['colors'][$colorType] ?? []);
            if ($value === [] && $parentValue !== []) {
                $this->properties['colors'][$colorType] = $styles->properties['colors'][$colorType];
            }
        }
        if (!\is_null($this->properties['options']['bold'] ?? null) || !\is_null($styles->properties['options']['bold'] ?? null)) {
            $this->properties['options']['bold'] = $this->properties['options']['bold'] ?? $styles->properties['options']['bold'] ?? \false;
        }
        return $this;
    }
    /**
     * Adds a background color to the element.
     */
    public final function bg(string $color, int $variant = 0) : self
    {
        return $this->with(['colors' => ['bg' => $this->getColorVariant($color, $variant)]]);
    }
    /**
     * Adds a bold style to the element.
     */
    public final function fontBold() : self
    {
        return $this->with(['options' => ['bold' => \true]]);
    }
    /**
     * Removes the bold style on the element.
     */
    public final function fontNormal() : self
    {
        return $this->with(['options' => ['bold' => \false]]);
    }
    /**
     * Adds a bold style to the element.
     */
    public final function strong() : self
    {
        $this->styleModifiers[__METHOD__] = static function ($text) : string {
            return \sprintf("\x1b[1m%s\x1b[0m", $text);
        };
        return $this;
    }
    /**
     * Adds an italic style to the element.
     */
    public final function italic() : self
    {
        $this->styleModifiers[__METHOD__] = static function ($text) : string {
            return \sprintf("\x1b[3m%s\x1b[0m", $text);
        };
        return $this;
    }
    /**
     * Adds an underline style.
     */
    public final function underline() : self
    {
        $this->styleModifiers[__METHOD__] = static function ($text) : string {
            return \sprintf("\x1b[4m%s\x1b[0m", $text);
        };
        return $this;
    }
    /**
     * Adds the given margin left to the element.
     */
    public final function ml(int $margin) : self
    {
        return $this->with(['styles' => ['ml' => $margin]]);
    }
    /**
     * Adds the given margin right to the element.
     */
    public final function mr(int $margin) : self
    {
        return $this->with(['styles' => ['mr' => $margin]]);
    }
    /**
     * Adds the given margin bottom to the element.
     */
    public final function mb(int $margin) : self
    {
        return $this->with(['styles' => ['mb' => $margin]]);
    }
    /**
     * Adds the given margin top to the element.
     */
    public final function mt(int $margin) : self
    {
        return $this->with(['styles' => ['mt' => $margin]]);
    }
    /**
     * Adds the given horizontal margin to the element.
     */
    public final function mx(int $margin) : self
    {
        return $this->with(['styles' => ['ml' => $margin, 'mr' => $margin]]);
    }
    /**
     * Adds the given vertical margin to the element.
     */
    public final function my(int $margin) : self
    {
        return $this->with(['styles' => ['mt' => $margin, 'mb' => $margin]]);
    }
    /**
     * Adds the given margin to the element.
     */
    public final function m(int $margin) : self
    {
        return $this->my($margin)->mx($margin);
    }
    /**
     * Adds the given padding left to the element.
     * @return static
     */
    public final function pl(int $padding)
    {
        return $this->with(['styles' => ['pl' => $padding]]);
    }
    /**
     * Adds the given padding right.
     * @return static
     */
    public final function pr(int $padding)
    {
        return $this->with(['styles' => ['pr' => $padding]]);
    }
    /**
     * Adds the given horizontal padding.
     */
    public final function px(int $padding) : self
    {
        return $this->pl($padding)->pr($padding);
    }
    /**
     * Adds the given padding top.
     * @return static
     */
    public final function pt(int $padding)
    {
        return $this->with(['styles' => ['pt' => $padding]]);
    }
    /**
     * Adds the given padding bottom.
     * @return static
     */
    public final function pb(int $padding)
    {
        return $this->with(['styles' => ['pb' => $padding]]);
    }
    /**
     * Adds the given vertical padding.
     */
    public final function py(int $padding) : self
    {
        return $this->pt($padding)->pb($padding);
    }
    /**
     * Adds the given padding.
     */
    public final function p(int $padding) : self
    {
        return $this->pt($padding)->pr($padding)->pb($padding)->pl($padding);
    }
    /**
     * Adds the given vertical margin to the childs, ignoring the first child.
     */
    public final function spaceY(int $space) : self
    {
        return $this->with(['styles' => ['spaceY' => $space]]);
    }
    /**
     * Adds the given horizontal margin to the childs, ignoring the first child.
     */
    public final function spaceX(int $space) : self
    {
        return $this->with(['styles' => ['spaceX' => $space]]);
    }
    /**
     * Adds a border on top of each element.
     */
    public final function borderT(int $width = 1) : self
    {
        if (!$this->element instanceof Hr) {
            throw new InvalidStyle('`border-t` can only be used on an "hr" element.');
        }
        $this->styleModifiers[__METHOD__] = function ($text, $styles) : string {
            $length = $this->getLength($text);
            if ($length < 1) {
                $margins = (int) ($styles['ml'] ?? 0) + ($styles['mr'] ?? 0);
                return \str_repeat('─', self::getParentWidth($this->properties['parentStyles'] ?? []) - $margins);
            }
            return \str_repeat('─', $length);
        };
        return $this;
    }
    /**
     * Adds a text alignment or color to the element.
     */
    public final function text(string $value, int $variant = 0) : self
    {
        if (\in_array($value, ['left', 'right', 'center'], \true)) {
            return $this->with(['styles' => ['text-align' => $value]]);
        }
        return $this->with(['colors' => ['fg' => $this->getColorVariant($value, $variant)]]);
    }
    /**
     * Truncates the text of the element.
     */
    public final function truncate(int $limit = 0, string $end = '…') : self
    {
        $this->textModifiers[__METHOD__] = function ($text, $styles) use($limit, $end) : string {
            $width = $styles['width'] ?? 0;
            if (\is_string($width)) {
                $width = self::calcWidthFromFraction($width, $styles, $this->properties['parentStyles'] ?? []);
            }
            [, $paddingRight, , $paddingLeft] = $this->getPaddings();
            $width -= $paddingRight + $paddingLeft;
            $limit = $limit > 0 ? $limit : $width;
            if ($limit === 0) {
                return $text;
            }
            $limit -= \mb_strwidth($end, 'UTF-8');
            if ($this->getLength($text) <= $limit) {
                return $text;
            }
            return \rtrim(self::trimText($text, $limit) . $end);
        };
        return $this;
    }
    /**
     * Forces the width of the element.
     * @param int|string $width
     * @return static
     */
    public final function w($width)
    {
        return $this->with(['styles' => ['width' => $width]]);
    }
    /**
     * Forces the element width to the full width of the terminal.
     * @return static
     */
    public final function wFull()
    {
        return $this->w('1/1');
    }
    /**
     * Removes the width set on the element.
     * @return static
     */
    public final function wAuto()
    {
        return $this->with(['styles' => ['width' => null]]);
    }
    /**
     * Defines a minimum width of an element.
     * @param int|string $width
     * @return static
     */
    public final function minW($width)
    {
        return $this->with(['styles' => ['minWidth' => $width]]);
    }
    /**
     * Defines a maximum width of an element.
     * @param int|string $width
     * @return static
     */
    public final function maxW($width)
    {
        return $this->with(['styles' => ['maxWidth' => $width]]);
    }
    /**
     * Makes the element's content uppercase.
     */
    public final function uppercase() : self
    {
        $this->textModifiers[__METHOD__] = static function ($text) : string {
            return \mb_strtoupper($text, 'UTF-8');
        };
        return $this;
    }
    /**
     * Makes the element's content lowercase.
     */
    public final function lowercase() : self
    {
        $this->textModifiers[__METHOD__] = static function ($text) : string {
            return \mb_strtolower($text, 'UTF-8');
        };
        return $this;
    }
    /**
     * Makes the element's content capitalize.
     */
    public final function capitalize() : self
    {
        $this->textModifiers[__METHOD__] = static function ($text) : string {
            return \mb_convert_case($text, \MB_CASE_TITLE, 'UTF-8');
        };
        return $this;
    }
    /**
     * Makes the element's content in snakecase.
     */
    public final function snakecase() : self
    {
        $this->textModifiers[__METHOD__] = static function ($text) : string {
            return \mb_strtolower((string) \preg_replace(['/([a-z\\d])([A-Z])/', '/([^_])([A-Z][a-z])/'], '$1_$2', $text), 'UTF-8');
        };
        return $this;
    }
    /**
     * Makes the element's content with a line through.
     */
    public final function lineThrough() : self
    {
        $this->styleModifiers[__METHOD__] = static function ($text) : string {
            return \sprintf("\x1b[9m%s\x1b[0m", $text);
        };
        return $this;
    }
    /**
     * Makes the element's content invisible.
     */
    public final function invisible() : self
    {
        $this->styleModifiers[__METHOD__] = static function ($text) : string {
            return \sprintf("\x1b[8m%s\x1b[0m", $text);
        };
        return $this;
    }
    /**
     * Do not display element's content.
     */
    public final function hidden() : self
    {
        return $this->with(['styles' => ['display' => 'hidden']]);
    }
    /**
     * Makes a line break before the element's content.
     */
    public final function block() : self
    {
        return $this->with(['styles' => ['display' => 'block']]);
    }
    /**
     * Makes an element eligible to work with flex-1 element's style.
     */
    public final function flex() : self
    {
        return $this->with(['styles' => ['display' => 'flex']]);
    }
    /**
     * Makes an element grow and shrink as needed, ignoring the initial size.
     */
    public final function flex1() : self
    {
        return $this->with(['styles' => ['flex-1' => \true]]);
    }
    /**
     * Justifies childs along the element with an equal amount of space between.
     */
    public final function justifyBetween() : self
    {
        return $this->with(['styles' => ['justifyContent' => 'between']]);
    }
    /**
     * Justifies childs along the element with an equal amount of space between
     * each item and half around.
     */
    public final function justifyAround() : self
    {
        return $this->with(['styles' => ['justifyContent' => 'around']]);
    }
    /**
     * Justifies childs along the element with an equal amount of space around each item.
     */
    public final function justifyEvenly() : self
    {
        return $this->with(['styles' => ['justifyContent' => 'evenly']]);
    }
    /**
     * Justifies childs along the center of the container’s main axis.
     */
    public final function justifyCenter() : self
    {
        return $this->with(['styles' => ['justifyContent' => 'center']]);
    }
    /**
     * Repeats the string given until it fills all the content.
     */
    public final function contentRepeat(string $string) : self
    {
        $string = \preg_replace("/\\[?'?([^'|\\]]+)'?\\]?/", '$1', $string) ?? '';
        $this->textModifiers[__METHOD__] = static function () use($string) : string {
            return \str_repeat($string, (int) \floor(terminal()->width() / \mb_strlen($string, 'UTF-8')));
        };
        return $this->with(['styles' => ['contentRepeat' => \true]]);
    }
    /**
     * Prepends text to the content.
     */
    public final function prepend(string $string) : self
    {
        $this->textModifiers[__METHOD__] = static function ($text) use($string) : string {
            return $string . $text;
        };
        return $this;
    }
    /**
     * Appends text to the content.
     */
    public final function append(string $string) : self
    {
        $this->textModifiers[__METHOD__] = static function ($text) use($string) : string {
            return $text . $string;
        };
        return $this;
    }
    /**
     * Prepends the list style type to the content.
     */
    public final function list(string $type, int $index = 0) : self
    {
        if (!$this->element instanceof Ul && !$this->element instanceof Ol && !$this->element instanceof Li) {
            throw new InvalidStyle(\sprintf('Style list-none cannot be used with %s', $this->element !== null ? \get_class($this->element) : 'unknown element'));
        }
        if (!$this->element instanceof Li) {
            return $this;
        }
        switch ($type) {
            case 'square':
                return $this->prepend('▪ ');
            case 'disc':
                return $this->prepend('• ');
            case 'decimal':
                return $this->prepend(\sprintf('%d. ', $index));
            default:
                return $this;
        }
    }
    /**
     * Adds the given properties to the element.
     *
     * @param  array<string, mixed>  $properties
     */
    public function with(array $properties) : self
    {
        $this->properties = \array_replace_recursive($this->properties, $properties);
        return $this;
    }
    /**
     * Sets the href property to the element.
     */
    public final function href(string $href) : self
    {
        $href = \str_replace('%', '%%', $href);
        return $this->with(['href' => \array_filter([$href])]);
    }
    /**
     * Formats a given string.
     */
    public final function format(string $content) : string
    {
        foreach ($this->textModifiers as $modifier) {
            $content = $modifier($content, $this->properties['styles'] ?? [], $this->properties['parentStyles'] ?? []);
        }
        $content = $this->applyWidth($content);
        foreach ($this->styleModifiers as $modifier) {
            $content = $modifier($content, $this->properties['styles'] ?? []);
        }
        return $this->applyStyling($content);
    }
    /**
     * Get the format string including required styles.
     */
    private function getFormatString() : string
    {
        $styles = [];
        /** @var array<int, string> $href */
        $href = $this->properties['href'] ?? [];
        if ($href !== []) {
            $styles[] = \sprintf('href=%s', \array_pop($href));
        }
        $colors = $this->properties['colors'] ?? [];
        foreach ($colors as $option => $content) {
            if (\in_array($option, ['fg', 'bg'], \true)) {
                $content = \is_array($content) ? \array_pop($content) : $content;
                $styles[] = "{$option}={$content}";
            }
        }
        $options = $this->properties['options'] ?? [];
        if ($options !== []) {
            $options = \array_keys(\array_filter($options, function ($option) {
                return $option === \true;
            }));
            $styles[] = \count($options) > 0 ? 'options=' . \implode(',', $options) : 'options=,';
        }
        // If there are no styles we don't need extra tags
        if ($styles === []) {
            return '%s%s%s%s%s';
        }
        return '%s<' . \implode(';', $styles) . '>%s%s%s</>%s';
    }
    /**
     * Get the margins applied to the element.
     *
     * @return array{0: int, 1: int, 2: int, 3: int}
     */
    private function getMargins() : array
    {
        $isFirstChild = (bool) $this->properties['isFirstChild'];
        $spaceY = $this->properties['parentStyles']['spaceY'] ?? [];
        $spaceY = !$isFirstChild ? \end($spaceY) : 0;
        $spaceX = $this->properties['parentStyles']['spaceX'] ?? [];
        $spaceX = !$isFirstChild ? \end($spaceX) : 0;
        return [$spaceY > 0 ? $spaceY : $this->properties['styles']['mt'] ?? 0, $this->properties['styles']['mr'] ?? 0, $this->properties['styles']['mb'] ?? 0, $spaceX > 0 ? $spaceX : $this->properties['styles']['ml'] ?? 0];
    }
    /**
     * Get the paddings applied to the element.
     *
     * @return array{0: int, 1: int, 2: int, 3: int}
     */
    private function getPaddings() : array
    {
        return [$this->properties['styles']['pt'] ?? 0, $this->properties['styles']['pr'] ?? 0, $this->properties['styles']['pb'] ?? 0, $this->properties['styles']['pl'] ?? 0];
    }
    /**
     * It applies the correct width for the content.
     */
    private function applyWidth(string $content) : string
    {
        $styles = $this->properties['styles'] ?? [];
        $minWidth = $styles['minWidth'] ?? -1;
        $width = \max($styles['width'] ?? -1, $minWidth);
        $maxWidth = $styles['maxWidth'] ?? 0;
        if ($width < 0) {
            return $content;
        }
        if ($width === 0) {
            return '';
        }
        if (\is_string($width)) {
            $width = self::calcWidthFromFraction($width, $styles, $this->properties['parentStyles'] ?? []);
        }
        if ($maxWidth > 0) {
            $width = \min($styles['maxWidth'], $width);
        }
        $width -= ($styles['pl'] ?? 0) + ($styles['pr'] ?? 0);
        $length = $this->getLength($content);
        \preg_match_all("/\n+/", $content, $matches);
        $width *= \count($matches[0] ?? []) + 1;
        // @phpstan-ignore-line
        $width += \mb_strlen($matches[0][0] ?? '', 'UTF-8');
        if ($length <= $width) {
            $space = $width - $length;
            switch ($styles['text-align'] ?? '') {
                case 'right':
                    return \str_repeat(' ', $space) . $content;
                case 'center':
                    return \str_repeat(' ', (int) \floor($space / 2)) . $content . \str_repeat(' ', (int) \ceil($space / 2));
                default:
                    return $content . \str_repeat(' ', $space);
            }
        }
        return self::trimText($content, $width);
    }
    /**
     * It applies the styling for the content.
     */
    private function applyStyling(string $content) : string
    {
        $display = $this->properties['styles']['display'] ?? 'inline';
        if ($display === 'hidden') {
            return '';
        }
        $isFirstChild = (bool) $this->properties['isFirstChild'];
        [$marginTop, $marginRight, $marginBottom, $marginLeft] = $this->getMargins();
        [$paddingTop, $paddingRight, $paddingBottom, $paddingLeft] = $this->getPaddings();
        $content = (string) \preg_replace('/\\r[ \\t]?/', "\n", (string) \preg_replace('/\\n/', \str_repeat(' ', $marginRight + $paddingRight) . "\n" . \str_repeat(' ', $marginLeft + $paddingLeft), $content));
        $formatted = \sprintf($this->getFormatString(), \str_repeat(' ', $marginLeft), \str_repeat(' ', $paddingLeft), $content, \str_repeat(' ', $paddingRight), \str_repeat(' ', $marginRight));
        $empty = \str_replace($content, \str_repeat(' ', $this->getLength($content)), $formatted);
        $items = [];
        if (\in_array($display, ['block', 'flex'], \true) && !$isFirstChild) {
            $items[] = "\n";
        }
        if ($marginTop > 0) {
            $items[] = \str_repeat("\n", $marginTop);
        }
        if ($paddingTop > 0) {
            $items[] = $empty . "\n";
        }
        $items[] = $formatted;
        if ($paddingBottom > 0) {
            $items[] = "\n" . $empty;
        }
        if ($marginBottom > 0) {
            $items[] = \str_repeat("\n", $marginBottom);
        }
        return \implode('', $items);
    }
    /**
     * Get the length of the text provided without the styling tags.
     */
    public function getLength(?string $text = null) : int
    {
        return \mb_strlen(\preg_replace(self::STYLING_REGEX, '', $text ?? (($nullsafeVariable1 = $this->element) ? $nullsafeVariable1->toString() : null) ?? '') ?? '', 'UTF-8');
    }
    /**
     * Get the length of the element without margins.
     */
    public function getInnerWidth() : int
    {
        $innerLength = $this->getLength();
        [, $marginRight, , $marginLeft] = $this->getMargins();
        return $innerLength - $marginLeft - $marginRight;
    }
    /**
     * Get the constant variant color from Color class.
     */
    private function getColorVariant(string $color, int $variant) : string
    {
        if ($variant > 0) {
            $color .= '-' . $variant;
        }
        if (StyleRepository::has($color)) {
            return StyleRepository::get($color)->getColor();
        }
        $colorConstant = \mb_strtoupper(\str_replace('-', '_', $color), 'UTF-8');
        if (!\defined(Color::class . "::{$colorConstant}")) {
            throw new ColorNotFound($colorConstant);
        }
        return \constant(Color::class . "::{$colorConstant}");
    }
    /**
     * Calculates the width based on the fraction provided.
     *
     * @param  array<string, int>  $styles
     * @param  array<string, array<int, int|string>>  $parentStyles
     */
    private static function calcWidthFromFraction(string $fraction, array $styles, array $parentStyles) : int
    {
        $width = self::getParentWidth($parentStyles);
        \preg_match('/(\\d+)\\/(\\d+)/', $fraction, $matches);
        if (\count($matches) !== 3 || $matches[2] === '0') {
            throw new InvalidStyle(\sprintf('Style [%s] is invalid.', "w-{$fraction}"));
        }
        $width = (int) \floor($width * $matches[1] / $matches[2]);
        $width -= ($styles['ml'] ?? 0) + ($styles['mr'] ?? 0);
        return $width;
    }
    /**
     * Gets the width of the parent element.
     *
     * @param  array<string, array<int|string>>  $styles
     */
    public static function getParentWidth(array $styles) : int
    {
        $width = terminal()->width();
        foreach ($styles['width'] ?? [] as $index => $parentWidth) {
            $minWidth = (int) $styles['minWidth'][$index];
            $maxWidth = (int) $styles['maxWidth'][$index];
            $margins = (int) $styles['ml'][$index] + (int) $styles['mr'][$index];
            $parentWidth = \max($parentWidth, $minWidth);
            if ($parentWidth < 1) {
                $parentWidth = $width;
            } elseif (\is_int($parentWidth)) {
                $parentWidth += $margins;
            }
            \preg_match('/(\\d+)\\/(\\d+)/', (string) $parentWidth, $matches);
            $width = \count($matches) !== 3 ? (int) $parentWidth : (int) \floor($width * $matches[1] / $matches[2]);
            if ($maxWidth > 0) {
                $width = \min($maxWidth, $width);
            }
            $width -= $margins;
            $width -= (int) $styles['pl'][$index] + (int) $styles['pr'][$index];
        }
        return $width;
    }
    /**
     * It trims the text properly ignoring all escape codes and
     * `<bg;fg;options>` tags.
     */
    private static function trimText(string $text, int $width) : string
    {
        \preg_match_all(self::STYLING_REGEX, $text, $matches, \PREG_OFFSET_CAPTURE);
        $text = \rtrim(\mb_strimwidth(\preg_replace(self::STYLING_REGEX, '', $text) ?? '', 0, $width, '', 'UTF-8'));
        // @phpstan-ignore-next-line
        foreach ($matches[0] ?? [] as [$part, $index]) {
            $text = \substr($text, 0, $index) . $part . \substr($text, $index, null);
        }
        return $text;
    }
}
