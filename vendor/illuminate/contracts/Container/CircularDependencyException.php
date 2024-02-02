<?php

namespace Lines202402\Illuminate\Contracts\Container;

use Exception;
use Lines202402\Psr\Container\ContainerExceptionInterface;
class CircularDependencyException extends Exception implements ContainerExceptionInterface
{
    //
}
