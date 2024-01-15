<?php

namespace Lines202401\Illuminate\Contracts\Container;

use Exception;
use Lines202401\Psr\Container\ContainerExceptionInterface;
class CircularDependencyException extends Exception implements ContainerExceptionInterface
{
    //
}
