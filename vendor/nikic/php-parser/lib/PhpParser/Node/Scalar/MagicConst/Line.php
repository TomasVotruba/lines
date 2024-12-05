<?php

declare (strict_types=1);
namespace Lines202412\PhpParser\Node\Scalar\MagicConst;

use Lines202412\PhpParser\Node\Scalar\MagicConst;
class Line extends MagicConst
{
    public function getName() : string
    {
        return '__LINE__';
    }
    public function getType() : string
    {
        return 'Scalar_MagicConst_Line';
    }
}