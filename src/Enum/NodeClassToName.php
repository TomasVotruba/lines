<?php

declare(strict_types=1);

namespace Rector\FeatureCounter\Enum;

use PhpParser\Node\AttributeGroup;
use PhpParser\Node\Expr\ArrowFunction;
use PhpParser\Node\Expr\AssignOp\Coalesce;
use PhpParser\Node\Expr\BinaryOp\Spaceship;
use PhpParser\Node\Expr\Match_;
use PhpParser\Node\Expr\NullsafeMethodCall;
use PhpParser\Node\Expr\NullsafePropertyFetch;
use PhpParser\Node\Expr\Throw_;
use PhpParser\Node\IntersectionType;
use PhpParser\Node\NullableType;
use PhpParser\Node\PropertyHook;
use PhpParser\Node\Stmt\Declare_;
use PhpParser\Node\Stmt\Enum_;
use PhpParser\Node\UnionType;

final class NodeClassToName
{
    /**
     * @var array<class-string, string>
     */
    public const LIST = [
        PropertyHook::class => 'property hooks',
        IntersectionType::class => 'intersection types',
        Enum_::class => 'enum',
        UnionType::class => 'union types',
        Match_::class => 'match',
        NullsafePropertyFetch::class => 'null safe property fetch',
        NullsafeMethodCall::class => 'null safe method call',
        AttributeGroup::class => 'attributes ',
        Throw_::class => 'throw expression',
        ArrowFunction::class => 'arrow function',
        Coalesce::class => 'coalesce assign (??=)',
        NullableType::class => 'nullable type',
        \PhpParser\Node\Expr\BinaryOp\Coalesce::class => 'coalesce operator (??)',
        Spaceship::class => 'spaceship operator (<=>)',
        Declare_::class => 'declare strict_types',
    ];
}
