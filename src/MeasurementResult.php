<?php

declare(strict_types=1);

namespace TomasVotruba\Lines;

use TomasVotruba\Lines\Enum\CounterName;
use Webmozart\Assert\Assert;

final class MeasurementResult
{
    /**
     * @param array<string, mixed> $counts
     */
    public function __construct(
        private array $counts
    ) {
        Assert::allString(array_keys($counts));
    }

    public function getDirectories(): int
    {
        return $this->getCount(CounterName::DIRECTORIES) - 1;
    }

    public function getFiles(): int
    {
        return $this->getValue(CounterName::FILES);
    }

    public function getLines(): int
    {
        return $this->getValue(CounterName::LINES);
    }

    public function getCommentLines(): int
    {
        return $this->getValue(CounterName::COMMENT_LINES);
    }

    public function getNonCommentLines(): int
    {
        return $this->getLines() - $this->getCommentLines();
    }

    public function getLogicalLines(): int
    {
        return $this->getValue(CounterName::LOGICAL_LINES);
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
        return $this->getValue(CounterName::FUNCTION_LINES);
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
        return $this->getValue(CounterName::INTERFACES);
    }

    public function getTraits(): int
    {
        return $this->getValue(CounterName::TRAITS);
    }

    public function getClasses(): int
    {
        return $this->getValue(CounterName::CLASSES);
    }

    public function getMethods(): int
    {
        return $this->getNonStaticMethods() + $this->getStaticMethods();
    }

    public function getNonStaticMethods(): int
    {
        return $this->getValue(CounterName::NON_STATIC_METHODS);
    }

    public function getStaticMethods(): int
    {
        return $this->getValue(CounterName::STATIC_METHODS);
    }

    public function getPublicMethods(): int
    {
        return $this->getValue(CounterName::PUBLIC_METHODS);
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
        return $this->getValue(CounterName::PROTECTED_METHODS);
    }

    public function getPrivateMethods(): int
    {
        return $this->getValue(CounterName::PRIVATE_METHODS);
    }

    public function getFunctions(): int
    {
        return $this->getNamedFunctions() + $this->getAnonymousFunctions();
    }

    public function getNamedFunctions(): int
    {
        return $this->getValue(CounterName::NAMED_FUNCTIONS);
    }

    public function getAnonymousFunctions(): int
    {
        return $this->getValue(CounterName::ANONYMOUS_FUNCTIONS);
    }

    public function getConstants(): int
    {
        return $this->getGlobalConstants() + $this->getClassConstants();
    }

    public function getGlobalConstants(): int
    {
        return $this->getValue(CounterName::GLOBAL_CONSTANTS);
    }

    public function getPublicClassConstants(): int
    {
        return $this->getValue(CounterName::PUBLIC_CLASS_CONSTANTS);
    }

    public function getNonPublicClassConstants(): int
    {
        return $this->getValue(CounterName::NON_PUBLIC_CLASS_CONSTATNTS);
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

    /**
     * @param CounterName::* $key
     */
    private function getValue(string $key): mixed
    {
        return $this->counts[$key] ?? 0;
    }

    private function divide(int $x, int $y): float
    {
        return $y != 0 ? $x / $y : 0;
    }
}
