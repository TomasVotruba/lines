<?php

declare (strict_types=1);
namespace Lines202508\PhpParser\Node\Expr;

use Lines202508\PhpParser\Node;
use Lines202508\PhpParser\Node\MatchArm;
class Match_ extends Node\Expr
{
    /** @var Node\Expr Condition */
    public $cond;
    /** @var MatchArm[] */
    public $arms;
    /**
     * @param Node\Expr $cond Condition
     * @param MatchArm[] $arms
     * @param array<string, mixed> $attributes Additional attributes
     */
    public function __construct(Node\Expr $cond, array $arms = [], array $attributes = [])
    {
        $this->attributes = $attributes;
        $this->cond = $cond;
        $this->arms = $arms;
    }
    public function getSubNodeNames() : array
    {
        return ['cond', 'arms'];
    }
    public function getType() : string
    {
        return 'Expr_Match';
    }
}
