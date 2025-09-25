<?php

namespace Lines202509;

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
use Lines202509\Symfony\Polyfill\Php85 as p;
if (\PHP_VERSION_ID >= 80500) {
    return;
}
if (!\function_exists('Lines202509\\get_error_handler')) {
    function get_error_handler() : ?callable
    {
        return p\Php85::get_error_handler();
    }
}
if (!\function_exists('Lines202509\\get_exception_handler')) {
    function get_exception_handler() : ?callable
    {
        return p\Php85::get_exception_handler();
    }
}
if (!\function_exists('Lines202509\\array_first')) {
    function array_first(array $array)
    {
        return p\Php85::array_first($array);
    }
}
if (!\function_exists('Lines202509\\array_last')) {
    function array_last(array $array)
    {
        return p\Php85::array_last($array);
    }
}
