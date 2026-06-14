<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Lines202606\Symfony\Component\Console\ArgumentResolver\ValueResolver;

use Lines202606\Symfony\Component\Console\Attribute\Reflection\ReflectionMember;
use Lines202606\Symfony\Component\Console\Input\InputInterface;
use Lines202606\Symfony\Component\Stopwatch\Stopwatch;
/**
 * @author Robin Chalas <robin.chalas@gmail.com>
 */
final class TraceableValueResolver implements ValueResolverInterface
{
    /**
     * @var \Symfony\Component\Console\ArgumentResolver\ValueResolver\ValueResolverInterface
     */
    private $inner;
    /**
     * @var \Symfony\Component\Stopwatch\Stopwatch
     */
    private $stopwatch;
    public function __construct(ValueResolverInterface $inner, Stopwatch $stopwatch)
    {
        $this->inner = $inner;
        $this->stopwatch = $stopwatch;
    }
    public function resolve(string $argumentName, InputInterface $input, ReflectionMember $member) : iterable
    {
        $method = \get_class($this->inner) . '::' . __FUNCTION__;
        $this->stopwatch->start($method, 'command.argument_value_resolver');
        try {
            yield from $this->inner->resolve($argumentName, $input, $member);
        } finally {
            $this->stopwatch->stop($method);
        }
    }
}
