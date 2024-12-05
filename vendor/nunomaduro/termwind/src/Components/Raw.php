<?php

declare (strict_types=1);
namespace Lines202412\Termwind\Components;

/**
 * @internal
 */
final class Raw extends Element
{
    /**
     * Get the string representation of the element.
     */
    public function toString() : string
    {
        return \is_array($this->content) ? \implode('', $this->content) : $this->content;
    }
}
