<?php

namespace Lines202308\Illuminate\Contracts\Container;

use Exception;
use Lines202308\Psr\Container\ContainerExceptionInterface;
class CircularDependencyException extends Exception implements ContainerExceptionInterface
{
    //
}
