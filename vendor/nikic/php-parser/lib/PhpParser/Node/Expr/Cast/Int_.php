<?php

declare (strict_types=1);
namespace Lines202412\PhpParser\Node\Expr\Cast;

use Lines202412\PhpParser\Node\Expr\Cast;
class Int_ extends Cast
{
    public function getType() : string
    {
        return 'Expr_Cast_Int';
    }
}
