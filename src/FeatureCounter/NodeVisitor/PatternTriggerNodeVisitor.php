<?php

declare (strict_types=1);
namespace Lines202512\TomasVotruba\Lines\FeatureCounter\NodeVisitor;

use Lines202512\PhpParser\Modifiers;
use Lines202512\PhpParser\Node;
use Lines202512\PhpParser\Node\Arg;
use Lines202512\PhpParser\Node\Expr\CallLike;
use Lines202512\PhpParser\Node\FunctionLike;
use Lines202512\PhpParser\Node\Identifier;
use Lines202512\PhpParser\Node\NullableType;
use Lines202512\PhpParser\Node\Param;
use Lines202512\PhpParser\Node\Stmt\Class_;
use Lines202512\PhpParser\Node\Stmt\ClassConst;
use Lines202512\PhpParser\Node\Stmt\Property;
use Lines202512\PhpParser\Node\UnionType;
use Lines202512\PhpParser\NodeVisitorAbstract;
use Lines202512\TomasVotruba\Lines\FeatureCounter\Enum\FeatureName;
use Lines202512\TomasVotruba\Lines\FeatureCounter\ValueObject\FeatureCollector;
final class PatternTriggerNodeVisitor extends NodeVisitorAbstract
{
    /**
     * @readonly
     * @var \TomasVotruba\Lines\FeatureCounter\ValueObject\FeatureCollector
     */
    private $featureCollector;
    public function __construct(FeatureCollector $featureCollector)
    {
        $this->featureCollector = $featureCollector;
    }
    /**
     * @return null
     */
    public function enterNode(Node $node)
    {
        // nullable and union type
        // @todo separate from nullable
        // UnionType::class => 0,
        if ($node instanceof NullableType) {
            $this->featureCollector->structureCounterByPhpVersion['7.1'][FeatureName::NULLABLE_TYPE]++;
            return null;
        }
        if ($node instanceof UnionType) {
            $hasNullableType = \false;
            // has `Null`?
            foreach ($node->types as $type) {
                if ($type instanceof Identifier && $type->name === 'null') {
                    $hasNullableType = \true;
                }
            }
            if ($hasNullableType && \count($node->types) === 2) {
                $this->featureCollector->structureCounterByPhpVersion['7.1'][FeatureName::NULLABLE_TYPE]++;
            } else {
                $this->featureCollector->structureCounterByPhpVersion['8.0'][FeatureName::UNION_TYPES]++;
            }
            return null;
        }
        if ($node instanceof CallLike && $node->isFirstClassCallable()) {
            $this->featureCollector->structureCounterByPhpVersion['8.1'][FeatureName::FIRST_CLASS_CALLABLES]++;
            return null;
        }
        if ($node instanceof Param && $node->isPromoted()) {
            $this->featureCollector->structureCounterByPhpVersion['8.0'][FeatureName::PROPERTY_PROMOTION]++;
            return null;
        }
        if ($node instanceof Param && $node->isReadonly() || $node instanceof Property && $node->isReadonly()) {
            $this->featureCollector->structureCounterByPhpVersion['8.1'][FeatureName::READONLY_PROPERTY]++;
            return null;
        }
        if ($node instanceof Class_ && $node->isReadonly()) {
            $this->featureCollector->structureCounterByPhpVersion['8.2'][FeatureName::READONLY_CLASS]++;
            return null;
        }
        if ($node instanceof ClassConst && ($node->flags & Modifiers::VISIBILITY_MASK) !== 0) {
            $this->featureCollector->structureCounterByPhpVersion['7.1'][FeatureName::CLASS_CONSTANT_VISIBILITY]++;
            return null;
        }
        if ($node instanceof Identifier && $node->toString() === 'object') {
            $this->featureCollector->structureCounterByPhpVersion['7.2'][FeatureName::OBJECT_TYPE]++;
            return null;
        }
        if ($node instanceof ClassConst && $node->type instanceof Node) {
            $this->featureCollector->structureCounterByPhpVersion['8.3'][FeatureName::TYPED_CONSTANTS]++;
            return null;
        }
        if ($node instanceof Arg && $node->name instanceof Identifier) {
            $this->featureCollector->structureCounterByPhpVersion['8.0'][FeatureName::NAMED_ARGUMENTS]++;
            return null;
        }
        if ($node instanceof FunctionLike && $node->getReturnType() instanceof Node && ($node->getReturnType() instanceof Identifier && $node->getReturnType()->name === 'void')) {
            $this->featureCollector->structureCounterByPhpVersion['7.1'][FeatureName::VOID_RETURN_TYPE]++;
            return null;
        }
        if ($node instanceof Property && $node->type instanceof Node) {
            $this->featureCollector->structureCounterByPhpVersion['7.4'][FeatureName::TYPED_PROPERTIES]++;
            return null;
        }
        if ($node instanceof Param && $node->type instanceof Node) {
            $this->featureCollector->structureCounterByPhpVersion['7.0'][FeatureName::PARAMETER_TYPES]++;
            return null;
        }
        if ($node instanceof FunctionLike && $node->getReturnType() instanceof Node) {
            $this->featureCollector->structureCounterByPhpVersion['7.0'][FeatureName::RETURN_TYPES]++;
            return null;
        }
        return null;
    }
}
