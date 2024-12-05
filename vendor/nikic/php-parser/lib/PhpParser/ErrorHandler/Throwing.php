<?php

declare (strict_types=1);
namespace Lines202412\PhpParser\ErrorHandler;

use Lines202412\PhpParser\Error;
use Lines202412\PhpParser\ErrorHandler;
/**
 * Error handler that handles all errors by throwing them.
 *
 * This is the default strategy used by all components.
 */
class Throwing implements ErrorHandler
{
    public function handleError(Error $error)
    {
        throw $error;
    }
}