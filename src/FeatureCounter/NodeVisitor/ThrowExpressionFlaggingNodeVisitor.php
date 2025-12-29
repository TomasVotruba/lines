<?php declare(strict_types=1);

namespace TomasVotruba\Lines\FeatureCounter\NodeVisitor;

use PhpParser\Node;
use PhpParser\Node\Expr\Throw_;
use PhpParser\Node\Stmt\Expression;
use PhpParser\NodeVisitor;
use PhpParser\NodeVisitorAbstract;

final class ThrowExpressionFlaggingNodeVisitor extends NodeVisitorAbstract {
    public function enterNode(Node $node) {
        if ($node instanceof Expression && $node->expr instanceof Throw_) {
            return NodeVisitor::DONT_TRAVERSE_CHILDREN;
        }

        if ($node instanceof Throw_) {
            $node->setAttribute('is_throw_expression', true);
        }

        return null;
    }
}
