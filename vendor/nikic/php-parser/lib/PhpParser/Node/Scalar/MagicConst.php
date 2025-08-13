<?php

declare (strict_types=1);
namespace Lines202508\PhpParser\Node\Scalar;

use Lines202508\PhpParser\Node\Scalar;
abstract class MagicConst extends Scalar
{
    /**
     * Constructs a magic constant node.
     *
     * @param array<string, mixed> $attributes Additional attributes
     */
    public function __construct(array $attributes = [])
    {
        $this->attributes = $attributes;
    }
    public function getSubNodeNames() : array
    {
        return [];
    }
    /**
     * Get name of magic constant.
     *
     * @return string Name of magic constant
     */
    public abstract function getName() : string;
}
