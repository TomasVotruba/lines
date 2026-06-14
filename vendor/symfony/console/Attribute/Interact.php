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

use Lines202606\Symfony\Component\Console\Exception\LogicException;
#[\Attribute(\Attribute::TARGET_METHOD)]
class Interact implements InteractiveAttributeInterface
{
    /**
     * @var \ReflectionMethod
     */
    private $method;
    /**
     * @internal
     */
    public static function tryFrom(\ReflectionMethod $method) : ?self
    {
        /** @var self|null $self */
        if (!($self = ($nullsafeVariable1 = (\method_exists($method, 'getAttributes') ? $method->getAttributes(self::class) : [])[0] ?? null) ? $nullsafeVariable1->newInstance() : null)) {
            return null;
        }
        if (!$method->isPublic() || $method->isStatic()) {
            throw new LogicException(\sprintf('The interactive method "%s::%s()" must be public and non-static.', $method->class, $method->getName()));
        }
        if ('__invoke' === $method->getName()) {
            throw new LogicException(\sprintf('The "%s::__invoke()" method cannot be used as an interactive method.', $method->class));
        }
        $self->method = $method;
        return $self;
    }
    /**
     * @internal
     */
    public function getFunction(object $instance) : \ReflectionFunction
    {
        return new \ReflectionFunction($this->method->getClosure($instance));
    }
}
