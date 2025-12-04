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
use PhpParser\Node\Stmt\Property;
use PhpParser\Node\UnionType;

final class FeatureCollector
{
    /**
     * @var PhpFeature[]
     */
    private array $phpFeatures = [];

    public function __construct()
    {
        $this->phpFeatures[] = new PhpFeature(
            70000,
            'Parameter types',
            fn (Node $node): bool => $node instanceof Param && $node->type instanceof Node,
        );

        $this->phpFeatures[] = new PhpFeature(
            70000,
            'Return types',
            fn (Node $node): bool => $node instanceof FunctionLike && $node->getReturnType() !== null,
        );

        $this->phpFeatures[] = new PhpFeature(
            70400,
            'Typed properties',
            fn (Node $node): bool => $node instanceof Property && $node->type instanceof Node,
        );

        $this->phpFeatures[] = new PhpFeature(
            70000,
            'Strict declares',
            fn (Node $node): bool => $node instanceof Declare_,
        );

        $this->phpFeatures[] = new PhpFeature(
            70000,
            'Space ship <=> operator ',
            fn (Node $node): bool => $node instanceof Spaceship,
        );

        $this->phpFeatures[] = new PhpFeature(
            70100,
            'Nullable type (?type)',
            function (Node $node): bool {
                if ($node instanceof NullableType) {
                    return true;
                }

                if (! $node instanceof UnionType) {
                    return false;
                }

                // include here, count as nullable type
                return self::isNullableUnionType($node);
            }
        );

        $this->phpFeatures[] = new PhpFeature(
            70100,
            'Void return type',
            fn (Node $node): bool => $node instanceof FunctionLike && $node->getReturnType() instanceof Identifier && $node->getReturnType()
                ->name === 'void',
        );

        $this->phpFeatures[] = new PhpFeature(
            70200,
            'Object type',
            fn (Node $node): bool => $node instanceof Identifier && $node->toString() === 'object',
        );

        $this->phpFeatures[] = new PhpFeature(
            70300,
            'Coalesce ?? operator',
            fn (Node $node): bool => $node instanceof \PhpParser\Node\Expr\BinaryOp\Coalesce,
        );

        // class constant visibility
        $this->phpFeatures[] = new PhpFeature(
            70100,
            'Class constant visibility',
            fn (Node $node): bool => $node instanceof ClassConst && ($node->flags & Modifiers::VISIBILITY_MASK) !== 0,
        );

        $this->phpFeatures[] = new PhpFeature(
            80000,
            'Named arguments',
            fn (Node $node): bool => $node instanceof Arg && $node->name instanceof Identifier,
        );

        $this->phpFeatures[] = new PhpFeature(
            80100,
            'First-class callables',
            fn (Node $node): bool => $node instanceof CallLike && $node->isFirstClassCallable()
        );

        // readonly property
        $this->phpFeatures[] = new PhpFeature(
            80100,
            'Readonly property',
            fn (Node $node): bool => $node instanceof Property && $node->isReadonly(),
        );

        // readonly class
        $this->phpFeatures[] = new PhpFeature(
            80200,
            'Readonly class',
            fn (Node $node): bool => $node instanceof Class_ && $node->isReadonly(),
        );

        // class const visibility
        $this->phpFeatures[] = new PhpFeature(
            70100,
            'Class constant visibility',
            fn (Node $node): bool => ($node instanceof ClassConst && $node->flags & Modifiers::VISIBILITY_MASK) !== 0
        );

        // typed class constants
        $this->phpFeatures[] = new PhpFeature(
            80300,
            'Typed class constants',
            fn (Node $node): bool => $node instanceof ClassConst && $node->type instanceof Node,
        );

        // arrow function
        $this->phpFeatures[] = new PhpFeature(
            70400,
            'Arrow functions',
            fn (Node $node): bool => $node instanceof ArrowFunction,
        );

        // coalesce assign (??=)
        $this->phpFeatures[] = new PhpFeature(
            70400,
            'Coalesce assign (??=)',
            fn (Node $node): bool => $node instanceof Coalesce,
        );

        // union types
        $this->phpFeatures[] = new PhpFeature(
            80000,
            'Union types',
            function (Node $node): bool {
                if (! $node instanceof \PhpParser\Node\UnionType) {
                    return false;
                }

                // skip here, count as nullable type
                return ! self::isNullableUnionType($node);
            }
        );

        // intersection types
        $this->phpFeatures[] = new PhpFeature(
            80100,
            'Intersection types',
            fn (Node $node): bool => $node instanceof IntersectionType,
        );

        // property hooks
        $this->phpFeatures[] = new PhpFeature(
            80400,
            'Property hooks',
            fn (Node $node): bool => $node instanceof PropertyHook,
        );

        // match
        $this->phpFeatures[] = new PhpFeature(
            80000,
            'Match expression',
            fn (Node $node): bool => $node instanceof Node\Expr\Match_,
        );

        // nullsafe method call or property fetch
        $this->phpFeatures[] = new PhpFeature(
            80000,
            'Nullsafe method call or property fetch',
            fn (Node $node): bool => $node instanceof NullsafeMethodCall || $node instanceof NullsafePropertyFetch,
        );

        // attributes
        $this->phpFeatures[] = new PhpFeature(
            80000,
            'Attributes',
            fn (Node $node): bool => $node instanceof AttributeGroup,
        );

        // throw expression
        $this->phpFeatures[] = new PhpFeature(
            80000,
            'Throw expression',
            fn (Node $node): bool => $node instanceof Throw_,
        );

        // enums
        $this->phpFeatures[] = new PhpFeature(
            80100,
            'Enums',
            fn (Node $node): bool => $node instanceof Enum_,
        );

        // promoted properties
        $this->phpFeatures[] = new PhpFeature(
            80000,
            'Promoted properties',
            fn (Node $node): bool => $node instanceof Param && $node->isPromoted(),
        );

    }

    /**
     * @return array<string, int>
     */
    public function getFeatureCountByPhpVersion(): array
    {
        $phpFeaturesByVersion = [];
        foreach ($this->phpFeatures as $phpFeature) {
            if (! isset($phpFeaturesByVersion[$phpFeature->getPhpVersion()])) {
                $phpFeaturesByVersion[$phpFeature->getPhpVersion()] = 0;
            }

            $phpFeaturesByVersion[$phpFeature->getPhpVersion()] += $phpFeature->getCount();
        }

        return $phpFeaturesByVersion;
    }

    /**
     * @return array<string, array<string, int>>
     */
    public function getGroupedFeatureCountedByPhpVersion(): array
    {
        $data = $this->structureCounterByPhpVersion;

        foreach ($this->nodesTypesCounterByPhpVersion as $phpVersion => $nodesCountByNodeClass) {
            foreach ($nodesCountByNodeClass as $nodeClass => $count) {
                $description = NodeClassToName::LIST[$nodeClass];
                $data[$phpVersion][$description] = $count;
            }

            ksort($data[$phpVersion]);
        }

        ksort($data);

        return $data;
    }

    public function collectFromNode(Node $node): void
    {
        foreach ($this->phpFeatures as $phpFeature) {
            $callableNodeTrigger = $phpFeature->getNodeTrigger();
            if ($callableNodeTrigger($node)) {
                $phpFeature->increaseCount();
            }
        }
    }

    private static function isNullableUnionType(UnionType $unionType): bool
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
