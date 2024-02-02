<?php

declare (strict_types=1);
namespace Lines202402\PhpParser\Node\Scalar\MagicConst;

use Lines202402\PhpParser\Node\Scalar\MagicConst;
class Dir extends MagicConst
{
    public function getName() : string
    {
        return '__DIR__';
    }
    public function getType() : string
    {
        return 'Scalar_MagicConst_Dir';
    }
}
