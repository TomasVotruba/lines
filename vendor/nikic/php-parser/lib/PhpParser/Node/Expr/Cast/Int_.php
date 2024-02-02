<?php

declare (strict_types=1);
namespace Lines202402\PhpParser\Node\Expr\Cast;

use Lines202402\PhpParser\Node\Expr\Cast;
class Int_ extends Cast
{
    public function getType() : string
    {
        return 'Expr_Cast_Int';
    }
}
