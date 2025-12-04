<?php

declare(strict_types=1);

namespace TomasVotruba\Lines\FeatureCounter\NodeVisitor;

use PhpParser\Node;
use PhpParser\NodeVisitorAbstract;
use TomasVotruba\Lines\FeatureCounter\ValueObject\FeatureCollector;

final class FeatureCollectorNodeVisitor extends NodeVisitorAbstract
{
    public function __construct(
        private readonly FeatureCollector $featureCollector
    ) {
    }

    public function enterNode(Node $node)
    {
        $this->featureCollector->collectFromNode($node);
        return null;
    }
}
