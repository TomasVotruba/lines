<?php

declare (strict_types=1);
namespace Lines202512\TomasVotruba\Lines\FeatureCounter\Enum;

final class FeatureName
{
    /**
     * @var string
     */
    public const FIRST_CLASS_CALLABLES = 'first class callables';
    /**
     * @var string
     */
    public const READONLY_PROPERTY = 'read-only property';
    /**
     * @var string
     */
    public const PROPERTY_PROMOTION = 'constructor property promotion';
    /**
     * @var string
     */
    public const READONLY_CLASS = 'read-only classes';
    /**
     * @var string
     */
    public const CLASS_CONSTANT_VISIBILITY = 'class constant visibility';
    /**
     * @var string
     */
    public const OBJECT_TYPE = 'object type';
    /**
     * @var string
     */
    public const TYPED_CONSTANTS = 'typed class constants';
    /**
     * @var string
     */
    public const NAMED_ARGUMENTS = 'named arguments';
    /**
     * @var string
     */
    public const VOID_RETURN_TYPE = 'void return type';
    /**
     * @var string
     */
    public const TYPED_PROPERTIES = 'typed properties';
    /**
     * @var string
     */
    public const PARAMETER_TYPES = 'parameter types';
    /**
     * @var string
     */
    public const RETURN_TYPES = 'return types';
    /**
     * @var string
     */
    public const UNION_TYPES = 'union types';
    /**
     * @var string
     */
    public const NULLABLE_TYPE = 'nullable type';
}
