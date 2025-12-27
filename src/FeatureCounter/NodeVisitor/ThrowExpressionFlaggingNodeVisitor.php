<?php declare(strict_types=1);

namespace TomasVotruba\Lines\FeatureCounter\NodeVisitor;

use PhpParser\Node;
use PhpParser\Node\Expr\Throw_;
use PhpParser\Node\Stmt\Expression;
use PhpParser\NodeVisitorAbstract;

use function array_pop;
use function count;

final class ThrowExpressionFlaggingNodeVisitor extends NodeVisitorAbstract {
    /**
     * @var Node[]
     */
    private array $stack = [];

    public function beforeTraverse(array $nodes) {
        $this->stack = [];
    }

    public function enterNode(Node $node) {
        if ($node instanceof Throw_ && ! empty($this->stack)) {
            $parent = $this->stack[count($this->stack) - 1];

            if (! $parent instanceof Expression) {
                $node->setAttribute('is_throw_expression', true);
            }
        }

        $this->stack[] = $node;
    }

    public function leaveNode(Node $node) {
        array_pop($this->stack);
    }
}
