<?php

declare (strict_types=1);
namespace Lines202512\TomasVotruba\Lines\FeatureCounter\NodeVisitor;

use Lines202512\PhpParser\Node;
use Lines202512\PhpParser\NodeVisitorAbstract;
use Lines202512\TomasVotruba\Lines\FeatureCounter\ValueObject\FeatureCollector;
final class NodeInstanceNodeVisitor extends NodeVisitorAbstract
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
        foreach ($this->featureCollector->nodesTypesCounterByPhpVersion as $phpVersion => $nodeClassToCount) {
            foreach ($nodeClassToCount as $nodeClass => $count) {
                if (!$node instanceof $nodeClass) {
                    continue;
                }
                $this->featureCollector->nodesTypesCounterByPhpVersion[$phpVersion][$nodeClass]++;
                return null;
            }
        }
        return null;
    }
}
