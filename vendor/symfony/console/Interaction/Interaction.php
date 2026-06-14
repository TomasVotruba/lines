<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Lines202606\Symfony\Component\Console\Interaction;

use Lines202606\Symfony\Component\Console\Attribute\InteractiveAttributeInterface;
use Lines202606\Symfony\Component\Console\Attribute\MapInput;
use Lines202606\Symfony\Component\Console\Input\InputInterface;
use Lines202606\Symfony\Component\Console\Output\OutputInterface;
/**
 * @internal
 */
final class Interaction
{
    /**
     * @readonly
     * @var object
     */
    private $owner;
    /**
     * @readonly
     * @var \Symfony\Component\Console\Attribute\InteractiveAttributeInterface
     */
    private $attribute;
    public function __construct(object $owner, InteractiveAttributeInterface $attribute)
    {
        $this->owner = $owner;
        $this->attribute = $attribute;
    }
    /**
     * @param-immediately-invoked-callable $parameterResolver
     *
     * @param \Closure(\ReflectionFunction $function, InputInterface $input, OutputInterface $output): array $parameterResolver
     */
    public function interact(InputInterface $input, OutputInterface $output, \Closure $parameterResolver) : void
    {
        if ($this->owner instanceof MapInput) {
            $function = $this->attribute->getFunction($this->owner->createInstance($input));
            $function->invoke(...$parameterResolver($function, $input, $output));
            $this->owner->setValue($input, $function->getClosureThis());
            return;
        }
        $function = $this->attribute->getFunction($this->owner);
        $function->invoke(...$args = $parameterResolver($function, $input, $output));
        foreach ($function->getParameters() as $i => $parameter) {
            if (\is_object($args[$i]) && ($spec = MapInput::tryFrom($parameter))) {
                $spec->setValue($input, $args[$i]);
            }
        }
    }
}
