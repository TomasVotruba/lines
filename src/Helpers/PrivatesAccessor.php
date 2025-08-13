<?php

declare(strict_types=1);

namespace TomasVotruba\Lines\Helpers;

use ReflectionProperty;

final class PrivatesAccessor
{
    public static function propertyClosure(object $object, string $propertyName, callable $closure): void
    {
        $property = self::getPrivateProperty($object, $propertyName);

        // modify value
        $property = $closure($property);

        self::setPrivateProperty($object, $propertyName, $property);
    }

    private static function getPrivateProperty(object $object, string $propertyName): mixed
    {
        $reflectionProperty = new ReflectionProperty($object, $propertyName);

        return $reflectionProperty->getValue($object);
    }

    private static function setPrivateProperty(object $object, string $propertyName, mixed $value): void
    {
        $reflectionProperty = new ReflectionProperty($object, $propertyName);
        $reflectionProperty->setValue($object, $value);
    }
}
