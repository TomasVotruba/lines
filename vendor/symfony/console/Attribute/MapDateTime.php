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
 * Defines how a DateTime parameter should be resolved from command input.
 *
 * @author Robin Chalas <robin.chalas@gmail.com>
 */
#[\Attribute(\Attribute::TARGET_PARAMETER)]
class MapDateTime
{
    /**
     * @var string|null
     * @readonly
     */
    public $format;
    /**
     * @var string|null
     * @readonly
     */
    public $argument;
    /**
     * @var string|null
     * @readonly
     */
    public $option;
    /**
     * @param string|null $format   The DateTime format (@see https://php.net/datetime.format)
     * @param string|null $argument The argument name to read from (defaults to parameter name)
     * @param string|null $option   The option name to read from (mutually exclusive with $argument)
     */
    public function __construct(?string $format = null, ?string $argument = null, ?string $option = null)
    {
        $this->format = $format;
        $this->argument = $argument;
        $this->option = $option;
        if ($argument && $option) {
            throw new \LogicException('MapDateTime cannot specify both argument and option.');
        }
    }
}
