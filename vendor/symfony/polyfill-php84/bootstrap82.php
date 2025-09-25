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
use Lines202509\Symfony\Polyfill\Php84 as p;
if (\PHP_VERSION_ID >= 80400) {
    return;
}
if (\extension_loaded('intl') && !\function_exists('Lines202509\\grapheme_str_split')) {
    /**
     * @return mixed[]|false
     */
    function grapheme_str_split(string $string, int $length = 1)
    {
        return p\Php84::grapheme_str_split($string, $length);
    }
}
