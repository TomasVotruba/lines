<?php

declare (strict_types=1);
namespace Lines202509\PhpParser\Node\Expr\BinaryOp;

use Lines202509\PhpParser\Node\Expr\BinaryOp;
class Greater extends BinaryOp
{
    public function getOperatorSigil() : string
    {
        return '>';
    }
    public function getType() : string
    {
        return 'Expr_BinaryOp_Greater';
    }
}
