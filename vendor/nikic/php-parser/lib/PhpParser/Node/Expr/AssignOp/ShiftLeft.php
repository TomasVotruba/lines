<?php

declare (strict_types=1);
namespace Lines202508\PhpParser\Node\Expr\AssignOp;

use Lines202508\PhpParser\Node\Expr\AssignOp;
class ShiftLeft extends AssignOp
{
    public function getType() : string
    {
        return 'Expr_AssignOp_ShiftLeft';
    }
}
