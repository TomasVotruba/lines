<?php

namespace Lines202412\Illuminate\Contracts\Container;

use Exception;
use Lines202412\Psr\Container\ContainerExceptionInterface;
class CircularDependencyException extends Exception implements ContainerExceptionInterface
{
    //
}
