<?php

declare (strict_types=1);
namespace Lines202308\PhpParser\Node\Expr\AssignOp;

use Lines202308\PhpParser\Node\Expr\AssignOp;
class BitwiseXor extends AssignOp
{
    public function getType() : string
    {
        return 'Expr_AssignOp_BitwiseXor';
    }
}
