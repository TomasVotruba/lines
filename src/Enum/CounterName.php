<?php

declare(strict_types=1);

namespace TomasVotruba\Lines\Enum;

final class CounterName
{
    /**
     * @var string
     */
    public const FILES = 'files';

    /**
     * @var string
     */
    public const DIRECTORIES = 'directories';

    /**
     * @var string
     */
    public const LINES = 'lines';

    /**
     * @var string
     */
    public const COMMENT_LINES = 'comment lines';

    /**
     * @var string
     */
    public const LOGICAL_LINES = 'logical lines';

    /**
     * @var string
     */
    public const FUNCTION_LINES = 'function lines';

    /**
     * @var string
     */
    public const METHOD_LINES = 'method lines';

    /**
     * @var string
     */
    public const NON_STATIC_METHOD_CALLS = 'non-static method calls';

    /**
     * @var string
     */
    public const STATIC_METHOD_CALLS = 'static method calls';

    /**
     * @var string
     */
    public const INTERFACES = 'interfaces';

    /**
     * @var string
     */
    public const ABSTRACT_CLASSES = 'abstract classes';

    /**
     * @var string
     */
    public const NON_FINAL_CLASSES = 'non-final classes';

    /**
     * @var string
     */
    public const FINAL_CLASSES = 'final classes';

    /**
     * @var string
     */
    public const NON_STATIC_METHODS = 'non-static methods';

    /**
     * @var string
     */
    public const STATIC_METHODS = 'static methods';
}
