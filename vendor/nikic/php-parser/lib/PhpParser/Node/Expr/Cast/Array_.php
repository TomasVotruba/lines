<?php

declare (strict_types=1);
namespace Lines202308\PhpParser\Node\Expr\Cast;

use Lines202308\PhpParser\Node\Expr\Cast;
class Array_ extends Cast
{
    public function getType() : string
    {
        return 'Expr_Cast_Array';
    }
}
