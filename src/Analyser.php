<?php

declare(strict_types=1);

namespace TomasVotruba\Lines;

use Throwable;
use PhpParser\NodeTraverser;
use PhpParser\Parser;
use SebastianBergmann\LinesOfCode\Counter;
use TomasVotruba\Lines\NodeVisitor\StructureNodeVisitor;
use Webmozart\Assert\Assert;

/**
 * @see \TomasVotruba\Lines\Tests\AnalyserTest
 */
final class Analyser
{
    public function __construct(
        private readonly Parser $parser,
        private readonly Counter $counter,
    ) {
    }

    /**
     * @param string[] $filePaths
     */
    public function measureFiles(array $filePaths, ?callable $progressBarClosure = null): Measurements
    {
        $measurements = new Measurements();

        Assert::allString($filePaths);
        Assert::allFileExists($filePaths);

        foreach ($filePaths as $filePath) {
            $this->measureFile($measurements, $filePath);

            if (is_callable($progressBarClosure)) {
                $progressBarClosure();
            }
        }

        return $measurements;
    }

    private function measureFile(Measurements $measurements, string $filePath): void
    {
        Assert::fileExists($filePath);

        $fileContents = file_get_contents($filePath);
        Assert::string($fileContents);

        try {
            // avoid stop on invalid file contents
            $stmts = $this->parser->parse($fileContents);
        } catch (Throwable) {
            return;
        }

        if (! is_array($stmts)) {
            return;
        }

        $measurements->addFile($filePath);

        // measure structure
        $nodeTraverser = new NodeTraverser();
        $nodeTraverser->addVisitor(new StructureNodeVisitor($measurements));
        $nodeTraverser->traverse($stmts);

        // measure lines of code
        $initLinesOfCode = $this->resolveInitLinesOfCode($fileContents);
        $linesOfCode = $this->counter->countInAbstractSyntaxTree($initLinesOfCode, $stmts);

        $measurements->incrementLines($linesOfCode->linesOfCode());
        $measurements->incrementCommentLines($linesOfCode->commentLinesOfCode());
    }

    /**
     * @return int<0, max>
     */
    private function resolveInitLinesOfCode(string $fileContents): int
    {
        $linesOfCode = substr_count($fileContents, "\n");
        if ($linesOfCode === 0 && $fileContents !== '') {
            return 1;
        }

        return $linesOfCode;
    }
}
