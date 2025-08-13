<?php

namespace Lines202508\Illuminate\Contracts\Container;

use Exception;
use Lines202508\Psr\Container\ContainerExceptionInterface;
class CircularDependencyException extends Exception implements ContainerExceptionInterface
{
    //
}
