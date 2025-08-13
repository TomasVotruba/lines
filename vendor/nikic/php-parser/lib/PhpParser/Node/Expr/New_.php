<?php

declare (strict_types=1);
namespace Lines202508\PhpParser\Node\Expr;

use Lines202508\PhpParser\Node;
use Lines202508\PhpParser\Node\Arg;
use Lines202508\PhpParser\Node\Expr;
use Lines202508\PhpParser\Node\VariadicPlaceholder;
class New_ extends CallLike
{
    /** @var Node\Name|Expr|Node\Stmt\Class_ Class name */
    public $class;
    /** @var array<Arg|VariadicPlaceholder> Arguments */
    public $args;
    /**
     * Constructs a function call node.
     *
     * @param Node\Name|Expr|Node\Stmt\Class_ $class Class name (or class node for anonymous classes)
     * @param array<Arg|VariadicPlaceholder> $args Arguments
     * @param array<string, mixed> $attributes Additional attributes
     */
    public function __construct(Node $class, array $args = [], array $attributes = [])
    {
        $this->attributes = $attributes;
        $this->class = $class;
        $this->args = $args;
    }
    public function getSubNodeNames() : array
    {
        return ['class', 'args'];
    }
    public function getType() : string
    {
        return 'Expr_New';
    }
    public function getRawArgs() : array
    {
        return $this->args;
    }
}
