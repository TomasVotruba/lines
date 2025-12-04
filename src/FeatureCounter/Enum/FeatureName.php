<?php

declare(strict_types=1);

namespace TomasVotruba\Lines\FeatureCounter\Enum;

final class FeatureName
{
    public const string FIRST_CLASS_CALLABLES = 'first class callables';

    public const string READONLY_PROPERTY = 'read-only property';

    public const string PROPERTY_PROMOTION = 'constructor property promotion';

    public const string READONLY_CLASS = 'read-only classes';

    public const string CLASS_CONSTANT_VISIBILITY = 'class constant visibility';

    public const string OBJECT_TYPE = 'object type';

    public const string TYPED_CLASS_CONSTANTS = 'typed class constants';

    public const string NAMED_ARGUMENTS = 'named arguments';

    public const string VOID_RETURN_TYPE = 'void return type';

    public const string TYPED_PROPERTIES = 'typed properties';

    public const string PARAMETER_TYPES = 'parameter types';

    public const string RETURN_TYPES = 'return types';

    public const string UNION_TYPES = 'union types';

    public const string NULLABLE_TYPE = 'nullable type';

    public const string PROPERTY_HOOKS = 'property hooks';
}
