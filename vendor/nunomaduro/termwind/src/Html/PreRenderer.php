<?php

declare (strict_types=1);
namespace Lines202512\Termwind\Html;

use Lines202512\Termwind\Components\Element;
use Lines202512\Termwind\Termwind;
use Lines202512\Termwind\ValueObjects\Node;
/**
 * @internal
 */
final class PreRenderer
{
    /**
     * Gets HTML content from a given node and converts to the content element.
     */
    public function toElement(Node $node) : Element
    {
        $lines = \explode("\n", $node->getHtml());
        if (\reset($lines) === '') {
            \array_shift($lines);
        }
        if (\end($lines) === '') {
            \array_pop($lines);
        }
        $maxStrLen = \array_reduce($lines, static function (int $max, string $line) {
            return $max < \strlen($line) ? \strlen($line) : $max;
        }, 0);
        $styles = $node->getClassAttribute();
        $html = \array_map(static function (string $line) use($maxStrLen, $styles) {
            return (string) Termwind::div(\str_pad($line, $maxStrLen + 3), $styles);
        }, $lines);
        return Termwind::raw(\implode('', $html));
    }
}
