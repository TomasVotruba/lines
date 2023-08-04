<?php

declare(strict_types=1);

namespace TomasVotruba\Lines\NodeVisitor;

use PhpParser\Comment;
use PhpParser\Node\Expr;
use PhpParser\NodeVisitorAbstract;
use TomasVotruba\Lines\LinesOfCode;

/**
 * Inspired from
 * @see https://github.com/sebastianbergmann/lines-of-code/blob/main/src/LineCountingVisitor.php
 */
final class LinesNodeVisitor extends NodeVisitorAbstract
{
    private int $linesOfCode;

    /**
     * @var Comment[]
     */
    private array $comments = [];

    /**
     * @var int[]
     */
    private array $linesWithStatements = [];

    public function __construct(int $linesOfCode)
    {
        $this->linesOfCode = $linesOfCode;
    }

    public function enterNode(\PhpParser\Node $node): void
    {
        $this->comments = array_merge($this->comments, $node->getComments());

        if (! $node instanceof Expr) {
            return;
        }

        $this->linesWithStatements[] = $node->getStartLine();
    }

    public function result(): LinesOfCode
    {
        $commentLinesOfCode = 0;

        foreach ($this->comments() as $comment) {
            $commentLinesOfCode += ($comment->getEndLine() - $comment->getStartLine() + 1);
        }

        return new LinesOfCode(
            $this->linesOfCode,
            $commentLinesOfCode,
            $this->linesOfCode - $commentLinesOfCode,
            count(array_unique($this->linesWithStatements))
        );
    }

    /**
     * @return Comment[]
     */
    private function comments(): array
    {
        $comments = [];

        foreach ($this->comments as $comment) {
            $comments[$comment->getStartLine() . '_' . $comment->getStartTokenPos() . '_' . $comment->getEndLine() . '_' . $comment->getEndTokenPos()] = $comment;
        }

        return $comments;
    }
}
