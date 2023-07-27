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

    private int $namedFunctionCount = 0;

    private int $anonymousFunctionCount = 0;

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

    /**
     * @var int[]
     */
    private array $methodCountsPerClass = [];

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

    public function currentClassStop(): void
    {
        $this->methodCountsPerClass[] = $this->currentClassMethodCount;
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

    public function incrementNamedFunctions(): void
    {
        ++$this->namedFunctionCount;
    }

    public function incrementAnonymousFunctions(): void
    {
        ++$this->anonymousFunctionCount;
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

    public function getLogicalLines(): int
    {
        return $this->logicalLineCount;
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

        return $this->getClassLines() / count($this->classLineCountPerClass);
    }

    public function getMinimumClassLength(): int
    {
        return min($this->classLineCountPerClass);
    }

    public function getMaximumClassLength(): int
    {
        return max($this->classLineCountPerClass);
    }

    public function getAverageMethodLength(): float
    {
        if ($this->methodLineCountPerMethod === []) {
            return 0.0;
        }

        $totalMethodLineCount = array_sum($this->methodLineCountPerMethod);
        $average = $totalMethodLineCount / count($this->methodLineCountPerMethod);

        return NumberFormat::singleDecimal($average);
    }

    public function getMinimumMethodLength(): int
    {
        return min($this->methodLineCountPerMethod);
    }

    public function getMaximumMethodLength(): int
    {
        return max($this->methodLineCountPerMethod);
    }

    public function getAverageMethodCountPerClass(): float
    {
        if ($this->methodCountsPerClass === []) {
            return 0.0;
        }

        $totalMethodCount = array_sum($this->methodCountsPerClass);

        $average = $totalMethodCount / count($this->methodCountsPerClass);

        return NumberFormat::singleDecimal($average);
    }

    public function getMinimumMethodsPerClass(): int
    {
        return min($this->methodCountsPerClass);
    }

    public function getMaximumMethodsPerClass(): int
    {
        return max($this->methodCountsPerClass);
    }

    public function getFunctionLines(): int
    {
        return $this->functionLineCount;
    }

    public function getAverageFunctionLength(): float
    {
        if ($this->getFunctionCount() === 0) {
            return 0.0;
        }

        $average = $this->functionLineCount / $this->getFunctionCount();
        return NumberFormat::singleDecimal($average);
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
        return $this->namedFunctionCount + $this->anonymousFunctionCount;
    }

    public function getNamedFunctionCount(): int
    {
        return $this->namedFunctionCount;
    }

    public function getAnonymousFunctionCount(): int
    {
        return $this->anonymousFunctionCount;
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
}
