<?php

declare (strict_types=1);
namespace Lines202412\PhpParser\Node\Expr\AssignOp;

use Lines202412\PhpParser\Node\Expr\AssignOp;
class ShiftRight extends AssignOp
{
    public function getType() : string
    {
        return 'Expr_AssignOp_ShiftRight';
    }
}