<?php

declare (strict_types=1);
namespace Lines202509\PhpParser\ErrorHandler;

use Lines202509\PhpParser\Error;
use Lines202509\PhpParser\ErrorHandler;
/**
 * Error handler that handles all errors by throwing them.
 *
 * This is the default strategy used by all components.
 */
class Throwing implements ErrorHandler
{
    public function handleError(Error $error) : void
    {
        throw $error;
    }
}
