<?php

declare (strict_types=1);
/*
 * This file is part of sebastian/lines-of-code.
 *
 * (c) Sebastian Bergmann <sebastian@phpunit.de>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Lines202508\SebastianBergmann\LinesOfCode;

use function array_merge;
use function array_unique;
use function assert;
use function count;
use Lines202508\PhpParser\Comment;
use Lines202508\PhpParser\Node;
use Lines202508\PhpParser\Node\Expr;
use Lines202508\PhpParser\NodeVisitorAbstract;
final class LineCountingVisitor extends NodeVisitorAbstract
{
    /**
     * @var non-negative-int
     * @readonly
     */
    private $linesOfCode;
    /**
     * @var Comment[]
     */
    private $comments = [];
    /**
     * @var int[]
     */
    private $linesWithStatements = [];
    /**
     * @param non-negative-int $linesOfCode
     */
    public function __construct(int $linesOfCode)
    {
        $this->linesOfCode = $linesOfCode;
    }
    /**
     * @return null
     */
    public function enterNode(Node $node)
    {
        $this->comments = array_merge($this->comments, $node->getComments());
        if (!$node instanceof Expr) {
            return null;
        }
        $this->linesWithStatements[] = $node->getStartLine();
        return null;
    }
    public function result() : LinesOfCode
    {
        $commentLinesOfCode = 0;
        foreach ($this->comments() as $comment) {
            $commentLinesOfCode += $comment->getEndLine() - $comment->getStartLine() + 1;
        }
        $nonCommentLinesOfCode = $this->linesOfCode - $commentLinesOfCode;
        $logicalLinesOfCode = count(array_unique($this->linesWithStatements));
        assert($commentLinesOfCode >= 0);
        assert($nonCommentLinesOfCode >= 0);
        return new LinesOfCode($this->linesOfCode, $commentLinesOfCode, $nonCommentLinesOfCode, $logicalLinesOfCode);
    }
    /**
     * @return Comment[]
     */
    private function comments() : array
    {
        $comments = [];
        foreach ($this->comments as $comment) {
            $comments[$comment->getStartLine() . '_' . $comment->getStartTokenPos() . '_' . $comment->getEndLine() . '_' . $comment->getEndTokenPos()] = $comment;
        }
        return $comments;
    }
}
