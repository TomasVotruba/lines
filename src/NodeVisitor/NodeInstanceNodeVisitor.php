<?php

declare(strict_types=1);

namespace TomasVotruba\Lines\FeatureCounter\NodeVisitor;

use PhpParser\Node;
use PhpParser\NodeVisitorAbstract;
use TomasVotruba\Lines\FeatureCounter\ValueObject\FeatureCollector;

final class NodeInstanceNodeVisitor extends NodeVisitorAbstract
{
    public function __construct(
        private readonly FeatureCollector $featureCollector
    ) {
    }

    public function enterNode(Node $node): null
    {
        foreach ($this->featureCollector->nodesTypesCounterByPhpVersion as $phpVersion => $nodeClassToCount) {
            foreach ($nodeClassToCount as $nodeClass => $count) {
                if (! $node instanceof $nodeClass) {
                    continue;
                }

                $this->featureCollector->nodesTypesCounterByPhpVersion[$phpVersion][$nodeClass]++;
                return null;
            }
        }

        return null;
    }
}
