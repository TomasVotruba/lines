<?php

declare (strict_types=1);
namespace Lines202401\PhpParser\Node\Expr\AssignOp;

use Lines202401\PhpParser\Node\Expr\AssignOp;
class Pow extends AssignOp
{
    public function getType() : string
    {
        return 'Expr_AssignOp_Pow';
    }
}
