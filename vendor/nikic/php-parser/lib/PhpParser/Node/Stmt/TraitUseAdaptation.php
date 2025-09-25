<?php

declare (strict_types=1);
namespace Lines202509\PhpParser\Node\Stmt;

use Lines202509\PhpParser\Node;
abstract class TraitUseAdaptation extends Node\Stmt
{
    /** @var Node\Name|null Trait name */
    public $trait;
    /** @var Node\Identifier Method name */
    public $method;
}
