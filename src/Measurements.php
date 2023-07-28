<?php

declare(strict_types=1);

namespace TomasVotruba\Lines;

use TomasVotruba\Lines\Helpers\NumberFormat;

final class Measurements
{
    /**
     * @var int[]
     */
    private array $classLineCountPerClass = [];

    /**
     * @var int[]
     */
    private array $methodLineCountPerMethod = [];

    private int $currentClassLines = 0;

    private int $currentMethodLines = 0;

    private int $currentClassMethodCount = 0;

    /**
     * @var string[]
     */
    private array $directoryNames = [];

    private int $classCount = 0;

    private int $traitCount = 0;

    private int $interfaceCount = 0;

    private int $lineCount = 0;

    private int $fileCount = 0;

    private int $nonStaticMethodCount = 0;

    private int $staticMethodCount = 0;

    private int $publicMethodCount = 0;

    private int $protectedMethodCount = 0;

    private int $privateMethodCount = 0;

    private int $functionCount = 0;

    private int $globalConstantCount = 0;

    private int $publicClassConstantCount = 0;

    private int $nonPublicClassConstantCount = 0;

    private int $logicalLineCount = 0;

    private int $commentLineCount = 0;

    private int $functionLineCount = 0;

    /**
     * @var string[]
     */
    private array $namespaceNames = [];

    public function addFile(string $filename): void
    {
        $this->directoryNames[] = dirname($filename);

        ++$this->fileCount;
    }

    public function incrementLines(int $number): void
    {
        $this->lineCount += $number;
    }

    public function incrementCommentLines(int $number): void
    {
        $this->commentLineCount += $number;
    }

    public function incrementLogicalLines(): void
    {
        ++$this->logicalLineCount;
    }

    public function resetCurrentClass(): void
    {
        $this->classLineCountPerClass[] = $this->currentClassLines;

        $this->currentClassLines = 0;
        $this->currentClassMethodCount = 0;
    }

    public function incrementCurrentClassLines(): void
    {
        ++$this->currentClassLines;
    }

    public function currentMethodStart(): void
    {
        $this->currentMethodLines = 0;
    }

    public function currentClassIncrementMethods(): void
    {
        ++$this->currentClassMethodCount;
    }

    public function currentMethodIncrementLines(): void
    {
        ++$this->currentMethodLines;
    }

    public function currentMethodStop(): void
    {
        $this->methodLineCountPerMethod[] = $this->currentMethodLines;
    }

    public function incrementFunctionLines(): void
    {
        ++$this->functionLineCount;
    }

    public function addNamespace(string $namespace): void
    {
        $this->namespaceNames[] = $namespace;
    }

    public function incrementInterfaces(): void
    {
        ++$this->interfaceCount;
    }

    public function incrementTraits(): void
    {
        ++$this->traitCount;
    }

    public function incrementNonStaticMethods(): void
    {
        ++$this->nonStaticMethodCount;
    }

    public function incrementStaticMethods(): void
    {
        ++$this->staticMethodCount;
    }

    public function incrementPublicMethods(): void
    {
        ++$this->publicMethodCount;
    }

    public function incrementProtectedMethods(): void
    {
        ++$this->protectedMethodCount;
    }

    public function incrementPrivateMethods(): void
    {
        ++$this->privateMethodCount;
    }

    public function incrementFunctions(): void
    {
        ++$this->functionCount;
    }

    public function incrementGlobalConstants(): void
    {
        ++$this->globalConstantCount;
    }

    public function incrementPublicClassConstants(): void
    {
        ++$this->publicClassConstantCount;
    }

    public function incrementNonPublicClassConstants(): void
    {
        ++$this->nonPublicClassConstantCount;
    }

    public function incrementClasses(): void
    {
        ++$this->classCount;
    }

    public function getDirectories(): int
    {
        $uniqueDirectoryNames = array_unique($this->directoryNames);
        return count($uniqueDirectoryNames) - 1;
    }

    public function getFiles(): int
    {
        return $this->fileCount;
    }

    public function getLines(): int
    {
        return $this->lineCount;
    }

    public function getCommentLines(): int
    {
        return $this->commentLineCount;
    }

    public function getNonCommentLines(): int
    {
        return $this->lineCount - $this->commentLineCount;
    }

    /**
     * @api used in tests
     */
    public function getLogicalLines(): int
    {
        return $this->logicalLineCount;
    }

    public function getClassLinesRelative(): float
    {
        if ($this->logicalLineCount > 0) {
            $relative = ($this->getClassLines() / $this->logicalLineCount) * 100;
            return NumberFormat::singleDecimal($relative);
        }

        return 0.0;
    }

    public function getClassLines(): int
    {
        return array_sum($this->classLineCountPerClass);
    }

    public function getAverageClassLength(): float
    {
        if ($this->classLineCountPerClass === []) {
            return 0.0;
        }

        return $this->average($this->getClassLines(), count($this->classLineCountPerClass));
    }

    public function getMaxClassLength(): int
    {
        return max($this->classLineCountPerClass);
    }

    public function getAverageMethodLength(): float
    {
        if ($this->methodLineCountPerMethod === []) {
            return 0.0;
        }

        $totalMethodLineCount = array_sum($this->methodLineCountPerMethod);

        return $this->average($totalMethodLineCount, count($this->methodLineCountPerMethod));
    }

    public function getMaxMethodLength(): int
    {
        return max($this->methodLineCountPerMethod);
    }

    public function getFunctionLines(): int
    {
        return $this->functionLineCount;
    }

    public function getNotInClassesOrFunctions(): int
    {
        return $this->logicalLineCount - $this->getClassLines() - $this->functionLineCount;
    }

    public function getNamespaces(): int
    {
        $uniqueNamespaceNames = array_unique($this->namespaceNames);
        return count($uniqueNamespaceNames);
    }

    public function getInterfaces(): int
    {
        return $this->interfaceCount;
    }

    public function getTraits(): int
    {
        return $this->traitCount;
    }

    public function getClasses(): int
    {
        return $this->classCount;
    }

    public function getMethods(): int
    {
        return $this->nonStaticMethodCount + $this->staticMethodCount;
    }

    public function getNonStaticMethods(): int
    {
        return $this->nonStaticMethodCount;
    }

    public function getStaticMethods(): int
    {
        return $this->staticMethodCount;
    }

    public function getPublicMethods(): int
    {
        return $this->publicMethodCount;
    }

    /**
     * @api
     */
    public function getNonPublicMethods(): int
    {
        return $this->protectedMethodCount + $this->privateMethodCount;
    }

    public function getProtectedMethods(): int
    {
        return $this->protectedMethodCount;
    }

    public function getPrivateMethods(): int
    {
        return $this->privateMethodCount;
    }

    public function getFunctionCount(): int
    {
        return $this->functionCount;
    }

    public function getConstantCount(): int
    {
        return $this->globalConstantCount + $this->getClassConstants();
    }

    public function getGlobalConstantCount(): int
    {
        return $this->globalConstantCount;
    }

    public function getPublicClassConstants(): int
    {
        return $this->publicClassConstantCount;
    }

    public function getNonPublicClassConstants(): int
    {
        return $this->nonPublicClassConstantCount;
    }

    public function getClassConstants(): int
    {
        return $this->publicClassConstantCount + $this->nonPublicClassConstantCount;
    }

    public function getCommentLinesRelative(): float
    {
        if ($this->lineCount !== 0) {
            $relative = ($this->commentLineCount / $this->lineCount) * 100;
            return NumberFormat::singleDecimal($relative);
        }

        return 0.0;
    }

    public function getNonCommentLinesRelative(): float
    {
        if ($this->lineCount !== 0) {
            return $this->relative($this->getNonCommentLines(), $this->lineCount);
        }

        return 0.0;
    }

    public function getFunctionLinesRelative(): float
    {
        if ($this->logicalLineCount > 0) {
            return $this->relative($this->functionLineCount, $this->logicalLineCount);
        }

        return 0.0;
    }

    public function getNotInClassesOrFunctionsRelative(): float
    {
        if ($this->logicalLineCount > 0) {
            return $this->relative($this->getNotInClassesOrFunctions(), $this->logicalLineCount);
        }

        return 0.0;
    }

    private function relative(int $partialNumber, int $totalNumber): float
    {
        $relative = ($partialNumber / $totalNumber) * 100;
        return NumberFormat::singleDecimal($relative);
    }

    private function average(int $partialNumber, int $totalNumber): float
    {
        $relative = ($partialNumber / $totalNumber);
        return NumberFormat::singleDecimal($relative);
    }

    public function getStaticMethodsRelative(): float
    {
        if ($this->getMethods() > 0) {
            return $this->relative($this->staticMethodCount, $this->getMethods());
        }

        return 0.0;
    }

    public function getNonStaticMethodsRelative(): float
    {
        if ($this->getMethods() > 0) {
            return $this->relative($this->nonStaticMethodCount, $this->getMethods());
        }

        return 0.0;
    }
}
