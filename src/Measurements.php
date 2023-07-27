<?php

declare(strict_types=1);

namespace TomasVotruba\Lines;

use TomasVotruba\Lines\Enum\CounterName;

final class Measurements
{
    /**
     * @var array<CounterName::*, mixed>
     */
    private array $counts = [];

    private int $currentClassLines = 0;

    private int $currentMethodLines = 0;

    private int $currentNumberOfMethods = 0;

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

    public function currentClassReset(): void
    {
        // if ($this->currentClassLines > 0) {
        $this->addToArray(CounterName::CLASS_LINES, $this->currentClassLines);
        //}

        $this->currentClassLines = 0;
        $this->currentNumberOfMethods = 0;
    }

    public function currentClassStop(): void
    {
        $this->addToArray(CounterName::METHODS_PER_CLASS, $this->currentNumberOfMethods);
    }

    public function currentClassIncrementLines(): void
    {
        ++$this->currentClassLines;
    }

    public function currentMethodStart(): void
    {
        $this->currentMethodLines = 0;
    }

    public function currentClassIncrementMethods(): void
    {
        ++$this->currentNumberOfMethods;
    }

    public function currentMethodIncrementLines(): void
    {
        ++$this->currentMethodLines;
    }

    public function currentMethodStop(): void
    {
        $this->addToArray(CounterName::METHOD_LINES, $this->currentMethodLines);
    }

    public function incrementFunctionLines(): void
    {
        ++$this->functionLineCount;
    }

    public function addNamespace(string $namespace): void
    {
        $this->addUnique(CounterName::NAMESPACES, $namespace);
    }

    public function incrementInterfaces(): void
    {
        $this->interfaceCount++;
    }

    public function incrementTraits(): void
    {
        $this->traitCount++;
    }

    public function incrementNonStaticMethods(): void
    {
        $this->nonStaticMethodCount++;
    }

    public function incrementStaticMethods(): void
    {
        ++$this->staticMethodCount;
    }

    public function incrementPublicMethods(): void
    {
        $this->publicMethodCount++;
    }

    public function incrementProtectedMethods(): void
    {
        $this->protectedMethodCount++;
    }

    public function incrementPrivateMethods(): void
    {
        $this->privateMethodCount++;
    }

    public function incrementNamedFunctions(): void
    {
        $this->namedFunctionCount++;
    }

    public function incrementAnonymousFunctions(): void
    {
        $this->anonymousFunctionCount++;
    }

    public function incrementGlobalConstants(): void
    {
        $this->globalConstantCount++;
    }

    public function incrementPublicClassConstants(): void
    {
        ++$this->publicClassConstantCount;
    }

    public function incrementNonPublicClassConstants(): void
    {
        ++$this->nonPublicClassConstantCount;
    }

    /**
     * @param CounterName::* $key
     */
    private function addUnique(string $key, mixed $name): void
    {
        $this->check($key, []);
        $this->counts[$key][$name] = true;
    }

    /**
     * @param CounterName::* $key
     */
    private function addToArray(string $key, mixed $value): void
    {
        $this->check($key, []);
        $this->counts[$key][] = $value;
    }

    //    /**
    //     * @param CounterName::* $key
    //     */
    //    private function increment(string $key, int $number = 1): void
    //    {
    //        $this->check($key, 0);
    //        $this->counts[$key] += $number;
    //    }

    /**
     * @param CounterName::* $key
     * @param int|mixed[] $default
     */
    private function check(string $key, int|array $default): void
    {
        if (! isset($this->counts[$key])) {
            $this->counts[$key] = $default;
        }
    }

    public function incrementClasses(): void
    {
        $this->classCount++;
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
        return $this->getLines() - $this->getCommentLines();
    }

    public function getLogicalLines(): int
    {
        return $this->logicalLineCount;
    }

    public function getClassLines(): int
    {
        return $this->getSum(CounterName::CLASS_LINES);
    }

    public function getAverageClassLength(): float
    {
        return $this->getAverage(CounterName::CLASS_LINES);
    }

    public function getMinimumClassLength(): int
    {
        return $this->getMinimum(CounterName::CLASS_LINES);
    }

    public function getMaximumClassLength(): int
    {
        return $this->getMaximum(CounterName::CLASS_LINES);
    }

    public function getAverageMethodLength(): float
    {
        return $this->getAverage(CounterName::METHOD_LINES);
    }

    public function getMinimumMethodLength(): int
    {
        return $this->getMinimum(CounterName::METHOD_LINES);
    }

    public function getMaximumMethodLength(): int
    {
        return $this->getMaximum(CounterName::METHOD_LINES);
    }

    public function getAverageMethodsPerClass(): float
    {
        return $this->getAverage(CounterName::METHODS_PER_CLASS);
    }

    public function getMinimumMethodsPerClass(): int
    {
        return $this->getMinimum(CounterName::METHODS_PER_CLASS);
    }

    public function getMaximumMethodsPerClass(): int
    {
        return $this->getMaximum(CounterName::METHODS_PER_CLASS);
    }

    public function getFunctionLines(): int
    {
        return $this->functionLineCount;
    }

    public function getAverageFunctionLength(): float
    {
        return $this->divide($this->getFunctionLines(), $this->getFunctions());
    }

    public function getNotInClassesOrFunctions(): int
    {
        return $this->getLogicalLines() - $this->getClassLines() - $this->getFunctionLines();
    }

    public function getNamespaces(): int
    {
        return $this->getCount(CounterName::NAMESPACES);
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
        return $this->getNonStaticMethods() + $this->getStaticMethods();
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
        return $this->getProtectedMethods() + $this->getPrivateMethods();
    }

    public function getProtectedMethods(): int
    {
        return $this->protectedMethodCount;
    }

    public function getPrivateMethods(): int
    {
        return $this->privateMethodCount;
    }

    public function getFunctions(): int
    {
        return $this->getNamedFunctions() + $this->getAnonymousFunctions();
    }

    public function getNamedFunctions(): int
    {
        return $this->namedFunctionCount;
    }

    public function getAnonymousFunctions(): int
    {
        return $this->anonymousFunctionCount;
    }

    public function getConstants(): int
    {
        return $this->getGlobalConstants() + $this->getClassConstants();
    }

    public function getGlobalConstants(): int
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
        return $this->getPublicClassConstants() + $this->getNonPublicClassConstants();
    }

    /**
     * @param CounterName::* $key
     */
    private function getAverage(string $key): float
    {
        $result = $this->divide($this->getSum($key), $this->getCount($key));
        return (float) number_format($result, 1);
    }

    /**
     * @param CounterName::* $key
     */
    private function getCount(string $key): int
    {
        return isset($this->counts[$key]) ? is_countable($this->counts[$key]) ? count($this->counts[$key]) : 0 : 0;
    }

    /**
     * @param CounterName::* $key
     */
    private function getSum(string $key): int
    {
        if (! isset($this->counts[$key])) {
            return 0;
        }

        return (int) array_sum($this->counts[$key]);
    }

    /**
     * @param CounterName::* $key
     */
    private function getMaximum(string $key): int
    {
        return isset($this->counts[$key]) ? max($this->counts[$key]) : 0;
    }

    /**
     * @param CounterName::* $key
     */
    private function getMinimum(string $key): int
    {
        return isset($this->counts[$key]) ? min($this->counts[$key]) : 0;
    }

    private function divide(int $x, int $y): float
    {
        return $y != 0 ? $x / $y : 0;
    }
}
