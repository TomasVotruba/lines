<?php

declare (strict_types=1);
namespace Lines202308\PhpParser\Node\Stmt;

use Lines202308\PhpParser\Node;
abstract class TraitUseAdaptation extends Node\Stmt
{
    /** @var Node\Name|null Trait name */
    public $trait;
    /** @var Node\Identifier Method name */
    public $method;
}
