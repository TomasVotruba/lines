<?php

namespace Lines202509\Illuminate\Contracts\Container;

use Exception;
use Lines202509\Psr\Container\ContainerExceptionInterface;
class CircularDependencyException extends Exception implements ContainerExceptionInterface
{
    //
}
