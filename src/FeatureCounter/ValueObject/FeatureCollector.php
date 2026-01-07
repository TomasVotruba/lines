<?php

declare(strict_types=1);

namespace TomasVotruba\Lines\FeatureCounter\ValueObject;

use PhpParser\Modifiers;
use PhpParser\Node;
use PhpParser\Node\Arg;
use PhpParser\Node\AttributeGroup;
use PhpParser\Node\Expr\ArrowFunction;
use PhpParser\Node\Expr\AssignOp\Coalesce;
use PhpParser\Node\Expr\BinaryOp\Spaceship;
use PhpParser\Node\Expr\CallLike;
use PhpParser\Node\Expr\Match_;
use PhpParser\Node\Expr\NullsafeMethodCall;
use PhpParser\Node\Expr\NullsafePropertyFetch;
use PhpParser\Node\Expr\Throw_;
use PhpParser\Node\FunctionLike;
use PhpParser\Node\Identifier;
use PhpParser\Node\IntersectionType;
use PhpParser\Node\NullableType;
use PhpParser\Node\Param;
use PhpParser\Node\PropertyHook;
use PhpParser\Node\Stmt\Class_;
use PhpParser\Node\Stmt\ClassConst;
use PhpParser\Node\Stmt\Declare_;
use PhpParser\Node\Stmt\Enum_;
use PhpParser\Node\Stmt\Expression;
use PhpParser\Node\Stmt\Property;
use PhpParser\Node\UnionType;
use TomasVotruba\Lines\FeatureCounter\Enum\PhpVersion;

final class FeatureCollector
{
    /**
     * @var PhpFeature[]
     */
    private array $phpFeatures = [];

    public function __construct()
    {
        $this->phpFeatures[] = new PhpFeature(
            PhpVersion::PHP_70,
            'Parameter types',
            fn (Node $node): bool => $node instanceof Param && $node->type instanceof Node,
        );

        $this->phpFeatures[] = new PhpFeature(
            PhpVersion::PHP_70,
            'Return types',
            fn (Node $node): bool => $node instanceof FunctionLike && $node->getReturnType() !== null,
        );

        $this->phpFeatures[] = new PhpFeature(
            PhpVersion::PHP_74,
            'Typed properties',
            fn (Node $node): bool => $node instanceof Property && $node->type instanceof Node,
        );

        $this->phpFeatures[] = new PhpFeature(
            PhpVersion::PHP_70,
            'Strict declares',
            fn (Node $node): bool => $node instanceof Declare_,
        );

        $this->phpFeatures[] = new PhpFeature(
            PhpVersion::PHP_70,
            'Space ship <=> operator ',
            fn (Node $node): bool => $node instanceof Spaceship,
        );

        $this->phpFeatures[] = new PhpFeature(
            PhpVersion::PHP_71,
            'Nullable type (?type)',
            function (Node $node): bool {
                if ($node instanceof NullableType) {
                    return true;
                }

                if (! $node instanceof UnionType) {
                    return false;
                }

                // include here, count as nullable type
                return $this->isNullableUnionType($node);
            }
        );

        $this->phpFeatures[] = new PhpFeature(
            PhpVersion::PHP_71,
            'Void return type',
            fn (Node $node): bool => $node instanceof FunctionLike && $node->getReturnType() instanceof Identifier && $node->getReturnType()
                ->name === 'void',
        );

        $this->phpFeatures[] = new PhpFeature(
            PhpVersion::PHP_72,
            'Object type',
            fn (Node $node): bool => $node instanceof Identifier && $node->toString() === 'object',
        );

        $this->phpFeatures[] = new PhpFeature(
            PhpVersion::PHP_70,
            'Coalesce ?? operator',
            fn (Node $node): bool => $node instanceof \PhpParser\Node\Expr\BinaryOp\Coalesce,
        );

        // class constant visibility
        $this->phpFeatures[] = new PhpFeature(
            PhpVersion::PHP_71,
            'Class constant visibility',
            fn (Node $node): bool => $node instanceof ClassConst && ($node->flags & Modifiers::VISIBILITY_MASK) !== 0,
        );

        $this->phpFeatures[] = new PhpFeature(
            PhpVersion::PHP_80,
            'Named arguments',
            fn (Node $node): bool => $node instanceof Arg && $node->name instanceof Identifier,
        );

        $this->phpFeatures[] = new PhpFeature(
            PhpVersion::PHP_81,
            'First-class callables',
            fn (Node $node): bool => $node instanceof CallLike && $node->isFirstClassCallable()
        );

        // readonly property
        $this->phpFeatures[] = new PhpFeature(
            PhpVersion::PHP_81,
            'Readonly property',
            fn (Node $node): bool => $node instanceof Property && $node->isReadonly(),
        );

        // readonly class
        $this->phpFeatures[] = new PhpFeature(
            PhpVersion::PHP_82,
            'Readonly class',
            fn (Node $node): bool => $node instanceof Class_ && $node->isReadonly(),
        );

        // typed class constants
        $this->phpFeatures[] = new PhpFeature(
            PhpVersion::PHP_83,
            'Typed class constants',
            fn (Node $node): bool => $node instanceof ClassConst && $node->type instanceof Node,
        );

        // arrow function
        $this->phpFeatures[] = new PhpFeature(
            PhpVersion::PHP_74,
            'Arrow functions',
            fn (Node $node): bool => $node instanceof ArrowFunction,
        );

        // coalesce assign (??=)
        $this->phpFeatures[] = new PhpFeature(
            PhpVersion::PHP_74,
            'Coalesce assign (??=)',
            fn (Node $node): bool => $node instanceof Coalesce,
        );

        // union types
        $this->phpFeatures[] = new PhpFeature(
            PhpVersion::PHP_80,
            'Union types',
            function (Node $node): bool {
                if (! $node instanceof UnionType) {
                    return false;
                }

                // skip here, count as nullable type
                return ! $this->isNullableUnionType($node);
            }
        );

        // intersection types
        $this->phpFeatures[] = new PhpFeature(
            PhpVersion::PHP_81,
            'Intersection types',
            fn (Node $node): bool => $node instanceof IntersectionType,
        );

        // property hooks
        $this->phpFeatures[] = new PhpFeature(
            PhpVersion::PHP_84,
            'Property hooks',
            fn (Node $node): bool => $node instanceof PropertyHook,
        );

        // match
        $this->phpFeatures[] = new PhpFeature(
            PhpVersion::PHP_80,
            'Match expression',
            fn (Node $node): bool => $node instanceof Match_,
        );

        $this->phpFeatures[] = new PhpFeature(
            PhpVersion::PHP_80,
            'Nullsafe method call/property fetch',
            fn (Node $node): bool => $node instanceof NullsafeMethodCall || $node instanceof NullsafePropertyFetch,
        );

        // attributes
        $this->phpFeatures[] = new PhpFeature(
            PhpVersion::PHP_80,
            'Attributes',
            fn (Node $node): bool => $node instanceof AttributeGroup,
        );

        // throw expression
        $this->phpFeatures[] = new PhpFeature(
            PhpVersion::PHP_80,
            'Throw expression',
            function (Node $node): bool {
                if ($node instanceof Expression && $node->expr instanceof Throw_) {
                    $node->expr->setAttribute('is_throw_statement', true);

                    return false;
                }

                return $node instanceof Throw_ && $node->getAttribute('is_throw_statement', false) === false;
            },
        );

        // enums
        $this->phpFeatures[] = new PhpFeature(
            PhpVersion::PHP_81,
            'Enums',
            fn (Node $node): bool => $node instanceof Enum_,
        );

        // promoted properties
        $this->phpFeatures[] = new PhpFeature(
            PhpVersion::PHP_80,
            'Promoted properties',
            fn (Node $node): bool => $node instanceof Param && $node->isPromoted(),
        );

    }

    /**
     * @return PhpFeature[]
     */
    public function getPhpFeatures(): array
    {
        // sort by php version first, just to normalize order
        usort(
            $this->phpFeatures,
            fn (PhpFeature $firstPhpFeature, PhpFeature $secondPhpFeature): int => version_compare(
                $firstPhpFeature->getPhpVersion(),
                $secondPhpFeature->getPhpVersion()
            )
        );

        return $this->phpFeatures;
    }

    private function isNullableUnionType(UnionType $unionType): bool
    {
        if (count($unionType->types) !== 2) {
            return false;
        }

        // has `Null`?
        foreach ($unionType->types as $type) {
            if ($type instanceof Identifier && strtolower($type->name) === 'null') {
                return true;
            }
        }

        return false;
    }
}
