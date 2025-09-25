<?php

declare (strict_types=1);
namespace Lines202509\PhpParser\Node\Expr\Cast;

use Lines202509\PhpParser\Node\Expr\Cast;
class Int_ extends Cast
{
    // For use in "kind" attribute
    public const KIND_INT = 1;
    // "int" syntax
    public const KIND_INTEGER = 2;
    // "integer" syntax
    public function getType() : string
    {
        return 'Expr_Cast_Int';
    }
}
