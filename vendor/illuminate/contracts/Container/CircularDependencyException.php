<?php

namespace Lines202307\Illuminate\Contracts\Container;

use Exception;
use Lines202307\Psr\Container\ContainerExceptionInterface;
class CircularDependencyException extends Exception implements ContainerExceptionInterface
{
    //
}
