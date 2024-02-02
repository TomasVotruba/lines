<?php

declare (strict_types=1);
namespace Lines202402\PhpParser\Node\Expr\AssignOp;

use Lines202402\PhpParser\Node\Expr\AssignOp;
class Div extends AssignOp
{
    public function getType() : string
    {
        return 'Expr_AssignOp_Div';
    }
}
