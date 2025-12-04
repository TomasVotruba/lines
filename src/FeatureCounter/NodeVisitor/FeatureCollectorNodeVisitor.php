<?php

declare (strict_types=1);
namespace Lines202512\TomasVotruba\Lines\FeatureCounter\NodeVisitor;

use Lines202512\PhpParser\Node;
use Lines202512\PhpParser\NodeVisitorAbstract;
use Lines202512\TomasVotruba\Lines\FeatureCounter\ValueObject\FeatureCollector;
final class FeatureCollectorNodeVisitor extends NodeVisitorAbstract
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
    public function enterNode(Node $node)
    {
        foreach ($this->featureCollector->getPhpFeatures() as $phpFeature) {
            $callableNodeTrigger = $phpFeature->getNodeTrigger();
            if ($callableNodeTrigger($node)) {
                $phpFeature->increaseCount();
            }
        }
        return null;
    }
}
