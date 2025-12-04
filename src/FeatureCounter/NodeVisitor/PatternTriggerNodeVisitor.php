<?php

declare(strict_types=1);

namespace TomasVotruba\Lines\FeatureCounter\NodeVisitor;

use PhpParser\Node;
use PhpParser\Node\Identifier;
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

        return null;
    }
}
