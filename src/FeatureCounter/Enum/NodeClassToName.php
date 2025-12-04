<?php

declare (strict_types=1);
namespace Lines202512\TomasVotruba\Lines\FeatureCounter\Enum;

use Lines202512\PhpParser\Node\AttributeGroup;
use Lines202512\PhpParser\Node\Expr\ArrowFunction;
use Lines202512\PhpParser\Node\Expr\AssignOp\Coalesce;
use Lines202512\PhpParser\Node\Expr\BinaryOp\Spaceship;
use Lines202512\PhpParser\Node\Expr\Match_;
use Lines202512\PhpParser\Node\Expr\NullsafeMethodCall;
use Lines202512\PhpParser\Node\Expr\NullsafePropertyFetch;
use Lines202512\PhpParser\Node\Expr\Throw_;
use Lines202512\PhpParser\Node\IntersectionType;
use Lines202512\PhpParser\Node\NullableType;
use Lines202512\PhpParser\Node\PropertyHook;
use Lines202512\PhpParser\Node\Stmt\Declare_;
use Lines202512\PhpParser\Node\Stmt\Enum_;
use Lines202512\PhpParser\Node\UnionType;
final class NodeClassToName
{
    /**
     * @var array<class-string, string>
     */
    public const LIST = [PropertyHook::class => 'property hooks', IntersectionType::class => 'intersection types', Enum_::class => 'enum', UnionType::class => 'union types', Match_::class => 'match', NullsafePropertyFetch::class => 'null safe property fetch', NullsafeMethodCall::class => 'null safe method call', AttributeGroup::class => 'attributes ', Throw_::class => 'throw expression', ArrowFunction::class => 'arrow function', Coalesce::class => 'coalesce assign (??=)', NullableType::class => 'nullable type', \Lines202512\PhpParser\Node\Expr\BinaryOp\Coalesce::class => 'coalesce operator (??)', Spaceship::class => 'spaceship operator (<=>)', Declare_::class => 'declare strict_types'];
}
