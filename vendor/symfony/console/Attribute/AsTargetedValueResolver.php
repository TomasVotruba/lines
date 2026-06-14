<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Lines202606\Symfony\Component\Console\Attribute;

/**
 * Service tag to autoconfigure targeted value resolvers.
 */
#[\Attribute(\Attribute::TARGET_CLASS)]
class AsTargetedValueResolver
{
    /**
     * @var string|null
     * @readonly
     */
    public $name;
    /**
     * @param string|null $name The name with which the resolver can be targeted
     */
    public function __construct(?string $name = null)
    {
        $this->name = $name;
    }
}
