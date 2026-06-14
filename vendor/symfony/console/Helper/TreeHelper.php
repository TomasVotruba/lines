<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Lines202606\Symfony\Component\Console\Helper;

use Lines202606\Symfony\Component\Console\Output\OutputInterface;
/**
 * The TreeHelper class provides methods to display tree-like structures.
 *
 * @author Simon André <smn.andre@gmail.com>
 *
 * @implements \RecursiveIterator<int, TreeNode>
 */
final class TreeHelper implements \RecursiveIterator
{
    /**
     * @readonly
     * @var \Symfony\Component\Console\Output\OutputInterface
     */
    private $output;
    /**
     * @readonly
     * @var \Symfony\Component\Console\Helper\TreeNode
     */
    private $node;
    /**
     * @readonly
     * @var \Symfony\Component\Console\Helper\TreeStyle
     */
    private $style;
    /**
     * @var \Iterator<int, TreeNode>
     */
    private $children;
    private function __construct(OutputInterface $output, TreeNode $node, TreeStyle $style)
    {
        $this->output = $output;
        $this->node = $node;
        $this->style = $style;
        $this->children = new \IteratorIterator($this->node->getChildren());
        $this->children->rewind();
    }
    /**
     * @param string|\Symfony\Component\Console\Helper\TreeNode|null $root
     */
    public static function createTree(OutputInterface $output, $root = null, iterable $values = [], ?TreeStyle $style = null) : self
    {
        $node = $root instanceof TreeNode ? $root : new TreeNode($root ?? '');
        return new self($output, TreeNode::fromValues($values, $node), $style ?? TreeStyle::default());
    }
    public function current() : TreeNode
    {
        return $this->children->current();
    }
    public function key() : int
    {
        return $this->children->key();
    }
    public function next() : void
    {
        $this->children->next();
    }
    public function rewind() : void
    {
        $this->children->rewind();
    }
    public function valid() : bool
    {
        return $this->children->valid();
    }
    public function hasChildren() : bool
    {
        if (null === ($current = $this->current())) {
            return \false;
        }
        foreach ($current->getChildren() as $child) {
            return \true;
        }
        return \false;
    }
    public function getChildren() : \RecursiveIterator
    {
        return new self($this->output, $this->current(), $this->style);
    }
    /**
     * Recursively renders the tree to the output, applying the tree style.
     */
    public function render() : void
    {
        $treeIterator = new \RecursiveTreeIterator($this);
        $this->style->applyPrefixes($treeIterator);
        $this->output->writeln($this->node->getValue());
        $visited = new \SplObjectStorage();
        foreach ($treeIterator as $node) {
            $currentNode = $node instanceof TreeNode ? $node : $treeIterator->getInnerIterator()->current();
            if (isset($visited[$currentNode])) {
                throw new \LogicException(\sprintf('Cycle detected at node: "%s".', $currentNode->getValue()));
            }
            $visited[$currentNode] = \true;
            $this->output->writeln($node);
        }
    }
}
