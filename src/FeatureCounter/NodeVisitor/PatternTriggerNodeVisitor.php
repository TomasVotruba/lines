<?php

declare(strict_types=1);

namespace TomasVotruba\Lines\FeatureCounter\NodeVisitor;

use PhpParser\Modifiers;
use PhpParser\Node;
use PhpParser\Node\Arg;
use PhpParser\Node\Expr\CallLike;
use PhpParser\Node\FunctionLike;
use PhpParser\Node\Identifier;
use PhpParser\Node\NullableType;
use PhpParser\Node\Param;
use PhpParser\Node\Stmt\Class_;
use PhpParser\Node\Stmt\ClassConst;
use PhpParser\Node\Stmt\Property;
use PhpParser\Node\UnionType;
use PhpParser\NodeVisitorAbstract;
use TomasVotruba\Lines\FeatureCounter\Enum\FeatureName;
use TomasVotruba\Lines\FeatureCounter\ValueObject\FeatureCollector;

final class PatternTriggerNodeVisitor extends NodeVisitorAbstract
{
    public function __construct(
        private readonly FeatureCollector $featureCollector
    ) {
    }

    public function enterNode(Node $node): null
    {
        // nullable and union type
        // @todo separate from nullable
        // UnionType::class => 0,

        if ($node instanceof NullableType) {
            $this->featureCollector->structureCounterByPhpVersion['7.1'][FeatureName::NULLABLE_TYPE]++;

            return null;
        }

        if ($node instanceof UnionType) {
            $hasNullableType = false;

            // has `Null`?
            foreach ($node->types as $type) {
                if ($type instanceof Identifier && $type->name === 'null') {
                    $hasNullableType = true;
                }
            }

            if ($hasNullableType && count($node->types) === 2) {
                $this->featureCollector->structureCounterByPhpVersion['7.1'][FeatureName::NULLABLE_TYPE]++;
            } else {
                $this->featureCollector->structureCounterByPhpVersion['8.0'][FeatureName::UNION_TYPES]++;
            }

            return null;
        }

        if ($node instanceof Class_ && $node->isReadonly()) {
            $this->featureCollector->structureCounterByPhpVersion['8.2'][FeatureName::READONLY_CLASS]++;

            return null;
        }

        if ($node instanceof CallLike) {
            if ($node->isFirstClassCallable()) {
                $this->featureCollector->structureCounterByPhpVersion['8.1'][FeatureName::FIRST_CLASS_CALLABLES]++;
            }

            return null;
        }

        if ($node instanceof Param) {
            if ($node->isPromoted()) {
                $this->featureCollector->structureCounterByPhpVersion['8.0'][FeatureName::PROPERTY_PROMOTION]++;
            }

            if ($node->type instanceof Node) {
                $this->featureCollector->structureCounterByPhpVersion['7.0'][FeatureName::PARAMETER_TYPES]++;
            }

            return null;
        }

        if ($node instanceof Property) {
            if ($node->type instanceof Node) {
                $this->featureCollector->structureCounterByPhpVersion['7.4'][FeatureName::TYPED_PROPERTIES]++;
            }

            if ($node->isReadonly()) {
                $this->featureCollector->structureCounterByPhpVersion['8.1'][FeatureName::READONLY_PROPERTY]++;
            }

            return null;
        }

        if ($node instanceof ClassConst) {
            if ($node->type instanceof Node) {
                $this->featureCollector->structureCounterByPhpVersion['8.3'][FeatureName::TYPED_CLASS_CONSTANTS]++;
            }

            if (($node->flags & Modifiers::VISIBILITY_MASK) !== 0) {
                $this->featureCollector->structureCounterByPhpVersion['7.1'][FeatureName::CLASS_CONSTANT_VISIBILITY]++;
            }

            return null;
        }

//        if ($node instanceof Identifier && $node->toString() === 'object') {
//            $this->featureCollector->structureCounterByPhpVersion['7.2'][FeatureName::OBJECT_TYPE]++;
//
//            return null;
//        }

        if ($node instanceof Arg && $node->name instanceof Identifier) {
            $this->featureCollector->structureCounterByPhpVersion['8.0'][FeatureName::NAMED_ARGUMENTS]++;

            return null;
        }

        if ($node instanceof FunctionLike && $node->getReturnType() instanceof Node && ($node->getReturnType() instanceof Identifier && $node->getReturnType()->name === 'void')) {
            $this->featureCollector->structureCounterByPhpVersion['7.1'][FeatureName::VOID_RETURN_TYPE]++;
            return null;
        }

        if ($node instanceof FunctionLike && $node->getReturnType() instanceof Node) {
            $this->featureCollector->structureCounterByPhpVersion['7.0'][FeatureName::RETURN_TYPES]++;

            return null;
        }

        return null;
    }
}
