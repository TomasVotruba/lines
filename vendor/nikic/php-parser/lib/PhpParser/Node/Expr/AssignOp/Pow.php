<?php

declare (strict_types=1);
namespace Lines202512\PhpParser\Node\Expr\AssignOp;

use Lines202512\PhpParser\Node\Expr\AssignOp;
class Pow extends AssignOp
{
    public function getType() : string
    {
        return 'Expr_AssignOp_Pow';
    }
}
