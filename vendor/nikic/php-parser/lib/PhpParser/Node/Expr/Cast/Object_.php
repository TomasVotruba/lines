<?php

declare (strict_types=1);
namespace Lines202509\PhpParser\Node\Expr\Cast;

use Lines202509\PhpParser\Node\Expr\Cast;
class Object_ extends Cast
{
    public function getType() : string
    {
        return 'Expr_Cast_Object';
    }
}
