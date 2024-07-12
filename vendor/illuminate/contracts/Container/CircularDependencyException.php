<?php

namespace Lines202407\Illuminate\Contracts\Container;

use Exception;
use Lines202407\Psr\Container\ContainerExceptionInterface;
class CircularDependencyException extends Exception implements ContainerExceptionInterface
{
    //
}
