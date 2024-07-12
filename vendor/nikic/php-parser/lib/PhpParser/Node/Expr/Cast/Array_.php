<?php

declare (strict_types=1);
namespace Lines202407\PhpParser\Node\Expr\Cast;

use Lines202407\PhpParser\Node\Expr\Cast;
class Array_ extends Cast
{
    public function getType() : string
    {
        return 'Expr_Cast_Array';
    }
}
