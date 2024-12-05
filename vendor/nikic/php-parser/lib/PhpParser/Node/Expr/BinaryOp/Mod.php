<?php

declare (strict_types=1);
namespace Lines202412\PhpParser\Node\Expr\BinaryOp;

use Lines202412\PhpParser\Node\Expr\BinaryOp;
class Mod extends BinaryOp
{
    public function getOperatorSigil() : string
    {
        return '%';
    }
    public function getType() : string
    {
        return 'Expr_BinaryOp_Mod';
    }
}