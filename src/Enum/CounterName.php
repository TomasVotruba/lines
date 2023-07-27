<?php

declare(strict_types=1);

namespace TomasVotruba\Lines\Enum;

final class CounterName
{
    public const FUNCTION_LINES = 'function lines';

    public const METHOD_LINES = 'method lines';

    public const NON_STATIC_METHOD_CALLS = 'non-static method calls';

    public const STATIC_METHOD_CALLS = 'static method calls';

    public const NON_STATIC_METHODS = 'non-static methods';

    public const STATIC_METHODS = 'static methods';

    public const PUBLIC_METHODS = 'public methods';

    public const PRIVATE_METHODS = 'private methods';

    public const PROTECTED_METHODS = 'protected methods';

    public const NAMED_FUNCTIONS = 'named functions';

    public const ANONYMOUS_FUNCTIONS = 'anonymous functions';

    public const GLOBAL_CONSTANTS = 'global constants';

    public const PUBLIC_CLASS_CONSTANTS = 'public class constants';

    public const NON_PUBLIC_CLASS_CONSTATNTS = 'non-public class constants';

    public const CLASS_LINES = 'class lines';

    public const METHODS_PER_CLASS = 'methods per class';

    public const CONSTANT_NAMES = 'constant';

    public const NAMESPACES = 'namespaces';
}
