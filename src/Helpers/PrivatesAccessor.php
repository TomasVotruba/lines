<?php

declare (strict_types=1);
namespace Lines202407\TomasVotruba\Lines\Helpers;

use ReflectionProperty;
final class PrivatesAccessor
{
    public static function propertyClosure(object $object, string $propertyName, callable $closure) : void
    {
        $property = self::getPrivateProperty($object, $propertyName);
        // modify value
        $property = $closure($property);
        self::setPrivateProperty($object, $propertyName, $property);
    }
    /**
     * @return mixed
     */
    private static function getPrivateProperty(object $object, string $propertyName)
    {
        $reflectionProperty = new ReflectionProperty($object, $propertyName);
        $reflectionProperty->setAccessible(\true);
        return $reflectionProperty->getValue($object);
    }
    /**
     * @param mixed $value
     */
    private static function setPrivateProperty(object $object, string $propertyName, $value) : void
    {
        $reflectionProperty = new ReflectionProperty($object, $propertyName);
        $reflectionProperty->setAccessible(\true);
        $reflectionProperty->setValue($object, $value);
    }
}
