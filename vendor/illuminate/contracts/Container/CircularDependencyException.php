<?php

namespace Lines202512\Illuminate\Contracts\Container;

use Exception;
use Lines202512\Psr\Container\ContainerExceptionInterface;
class CircularDependencyException extends Exception implements ContainerExceptionInterface
{
    //
}
