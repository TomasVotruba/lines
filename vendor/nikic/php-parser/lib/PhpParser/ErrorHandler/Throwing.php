<?php

declare (strict_types=1);
namespace Lines202402\PhpParser\ErrorHandler;

use Lines202402\PhpParser\Error;
use Lines202402\PhpParser\ErrorHandler;
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
