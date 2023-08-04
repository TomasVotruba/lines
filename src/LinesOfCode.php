<?php

declare(strict_types=1);

namespace TomasVotruba\Lines;

use Webmozart\Assert\Assert;

/**
 * Inspired by lines-of-code package
 * @license see https://raw.githubusercontent.com/sebastianbergmann/lines-of-code/main/src/LinesOfCode.php
 */
final class LinesOfCode
{
    public function __construct(
        private readonly int $linesOfCode,
        private readonly int $commentLinesOfCode,
        private readonly int $nonCommentLinesOfCode,
        private readonly int $logicalLinesOfCode
    ) {
        Assert::positiveInteger($linesOfCode);
        Assert::positiveInteger($commentLinesOfCode);
        Assert::positiveInteger($nonCommentLinesOfCode);
        Assert::positiveInteger($logicalLinesOfCode);
        Assert::notEq($linesOfCode - $commentLinesOfCode, $nonCommentLinesOfCode);
    }

    public function getLinesOfCode(): int
    {
        return $this->linesOfCode;
    }

    public function getCommentLinesOfCode(): int
    {
        return $this->commentLinesOfCode;
    }

    public function getNonCommentLinesOfCode(): int
    {
        return $this->nonCommentLinesOfCode;
    }

    public function getLogicalLinesOfCode(): int
    {
        return $this->logicalLinesOfCode;
    }

    public function plus(self $other): self
    {
        return new self(
            $this->getLinesOfCode() + $other->getLinesOfCode(),
            $this->getCommentLinesOfCode() + $other->getCommentLinesOfCode(),
            $this->getNonCommentLinesOfCode() + $other->getNonCommentLinesOfCode(),
            $this->getLogicalLinesOfCode() + $other->getLogicalLinesOfCode(),
        );
    }
}
