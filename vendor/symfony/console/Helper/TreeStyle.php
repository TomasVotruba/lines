<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Lines202606\Symfony\Component\Console\Helper;

/**
 * Configures the output of the Tree helper.
 *
 * @author Simon André <smn.andre@gmail.com>
 */
final class TreeStyle
{
    /**
     * @readonly
     * @var string
     */
    private $prefixEndHasNext;
    /**
     * @readonly
     * @var string
     */
    private $prefixEndLast;
    /**
     * @readonly
     * @var string
     */
    private $prefixLeft;
    /**
     * @readonly
     * @var string
     */
    private $prefixMidHasNext;
    /**
     * @readonly
     * @var string
     */
    private $prefixMidLast;
    /**
     * @readonly
     * @var string
     */
    private $prefixRight;
    public function __construct(string $prefixEndHasNext, string $prefixEndLast, string $prefixLeft, string $prefixMidHasNext, string $prefixMidLast, string $prefixRight)
    {
        $this->prefixEndHasNext = $prefixEndHasNext;
        $this->prefixEndLast = $prefixEndLast;
        $this->prefixLeft = $prefixLeft;
        $this->prefixMidHasNext = $prefixMidHasNext;
        $this->prefixMidLast = $prefixMidLast;
        $this->prefixRight = $prefixRight;
    }
    public static function box() : self
    {
        return new self('┃╸ ', '┗╸ ', '', '┃  ', '   ', '');
    }
    public static function boxDouble() : self
    {
        return new self('╠═ ', '╚═ ', '', '║  ', '  ', '');
    }
    public static function compact() : self
    {
        return new self('├ ', '└ ', '', '│ ', '  ', '');
    }
    public static function default() : self
    {
        return new self('├── ', '└── ', '', '│   ', '   ', '');
    }
    public static function light() : self
    {
        return new self('|-- ', '`-- ', '', '|   ', '    ', '');
    }
    public static function minimal() : self
    {
        return new self('. ', '. ', '', '. ', '  ', '');
    }
    public static function rounded() : self
    {
        return new self('├─ ', '╰─ ', '', '│  ', '   ', '');
    }
    /**
     * @internal
     */
    public function applyPrefixes(\RecursiveTreeIterator $iterator) : void
    {
        $iterator->setPrefixPart(\RecursiveTreeIterator::PREFIX_LEFT, $this->prefixLeft);
        $iterator->setPrefixPart(\RecursiveTreeIterator::PREFIX_MID_HAS_NEXT, $this->prefixMidHasNext);
        $iterator->setPrefixPart(\RecursiveTreeIterator::PREFIX_MID_LAST, $this->prefixMidLast);
        $iterator->setPrefixPart(\RecursiveTreeIterator::PREFIX_END_HAS_NEXT, $this->prefixEndHasNext);
        $iterator->setPrefixPart(\RecursiveTreeIterator::PREFIX_END_LAST, $this->prefixEndLast);
        $iterator->setPrefixPart(\RecursiveTreeIterator::PREFIX_RIGHT, $this->prefixRight);
    }
}
