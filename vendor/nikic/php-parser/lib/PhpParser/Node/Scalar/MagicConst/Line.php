<?php

declare (strict_types=1);
namespace Lines202508\PhpParser\Node\Scalar\MagicConst;

use Lines202508\PhpParser\Node\Scalar\MagicConst;
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
