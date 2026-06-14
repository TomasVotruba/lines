<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Lines202606\Symfony\Component\Console\Attribute\Reflection;

use Lines202606\Symfony\Component\String\UnicodeString;
/**
 * @internal
 */
class ReflectionMember
{
    /**
     * @readonly
     * @var \ReflectionParameter|\ReflectionProperty
     */
    private $member;
    /**
     * @param \ReflectionParameter|\ReflectionProperty $member
     */
    public function __construct($member)
    {
        $this->member = $member;
    }
    /**
     * @template T of object
     *
     * @param class-string<T> $class
     *
     * @return T|null
     */
    public function getAttribute(string $class) : ?object
    {
        return ($nullsafeVariable1 = (\method_exists($this->member, 'getAttributes') ? $this->member->getAttributes($class, \ReflectionAttribute::IS_INSTANCEOF) : [])[0] ?? null) ? $nullsafeVariable1->newInstance() : null;
    }
    /**
     * @template T of object
     *
     * @param class-string<T> $class
     *
     * @return list<T>
     */
    public function getAttributes(string $class) : array
    {
        return \array_map(static function (\ReflectionAttribute $attribute) {
            return $attribute->newInstance();
        }, \method_exists($this->member, 'getAttributes') ? $this->member->getAttributes($class, \ReflectionAttribute::IS_INSTANCEOF) : []);
    }
    public function getSourceName() : string
    {
        if ($this->member instanceof \ReflectionProperty) {
            return $this->member->class;
        }
        $function = $this->member->getDeclaringFunction();
        if ($function instanceof \ReflectionMethod) {
            return $function->class . '::' . $function->name . '()';
        }
        return $function->name . '()';
    }
    public function getSourceThis() : ?object
    {
        if ($this->member instanceof \ReflectionParameter) {
            return $this->member->getDeclaringFunction()->getClosureThis();
        }
        return null;
    }
    public function getType() : ?\ReflectionType
    {
        return $this->member->getType();
    }
    public function getName() : string
    {
        return $this->member->getName();
    }
    public function hasDefaultValue() : bool
    {
        if ($this->member instanceof \ReflectionParameter) {
            return $this->member->isDefaultValueAvailable();
        }
        return $this->member->hasDefaultValue();
    }
    /**
     * @return mixed
     */
    public function getDefaultValue()
    {
        $defaultValue = $this->member->getDefaultValue();
        if ($defaultValue instanceof \BackedEnum) {
            return $defaultValue->value;
        }
        return $defaultValue;
    }
    public function isNullable() : bool
    {
        return (bool) (($nullsafeVariable2 = $this->member->getType()) ? $nullsafeVariable2->allowsNull() : null);
    }
    public function getMemberName() : string
    {
        return $this->member instanceof \ReflectionParameter ? 'parameter' : 'property';
    }
    public function isParameter() : bool
    {
        return $this->member instanceof \ReflectionParameter;
    }
    public function isVariadic() : bool
    {
        return $this->member instanceof \ReflectionParameter && $this->member->isVariadic();
    }
    public function isProperty() : bool
    {
        return $this->member instanceof \ReflectionProperty;
    }
    /**
     * @return \ReflectionParameter|\ReflectionProperty
     */
    public function getMember()
    {
        return $this->member;
    }
    public function getInputName() : string
    {
        return (new UnicodeString($this->member->getName()))->kebab()->toString();
    }
}
