<?php

declare (strict_types=1);
namespace Lines202407\PhpParser\Node\Expr\BinaryOp;

use Lines202407\PhpParser\Node\Expr\BinaryOp;
class ShiftRight extends BinaryOp
{
    public function getOperatorSigil() : string
    {
        return '>>';
    }
    public function getType() : string
    {
        return 'Expr_BinaryOp_ShiftRight';
    }
}
