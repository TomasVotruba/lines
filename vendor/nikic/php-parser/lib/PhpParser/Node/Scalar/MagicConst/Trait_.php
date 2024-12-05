<?php

declare (strict_types=1);
namespace Lines202412\PhpParser\Node\Scalar\MagicConst;

use Lines202412\PhpParser\Node\Scalar\MagicConst;
class Trait_ extends MagicConst
{
    public function getName() : string
    {
        return '__TRAIT__';
    }
    public function getType() : string
    {
        return 'Scalar_MagicConst_Trait';
    }
}