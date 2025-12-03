<?php

declare(strict_types=1);

namespace TomasVotruba\Lines\FeatureCounter\Enum;

final class FeatureName
{
    public const FIRST_CLASS_CALLABLES = 'first class callables';

    public const READONLY_PROPERTY = 'read-only property';

    public const PROPERTY_PROMOTION = 'constructor property promotion';

    public const READONLY_CLASS = 'read-only classes';

    public const CLASS_CONSTANT_VISIBILITY = 'class constant visibility';

    public const OBJECT_TYPE = 'object type';

    public const TYPED_CONSTANTS = 'typed class constants';

    public const NAMED_ARGUMENTS = 'named arguments';

    public const VOID_RETURN_TYPE = 'void return type';

    public const TYPED_PROPERTIES = 'typed properties';

    public const PARAMETER_TYPES = 'parameter types';

    public const RETURN_TYPES = 'return types';

    public const UNION_TYPES = 'union types';

    public const NULLABLE_TYPE = 'nullable type';
}
