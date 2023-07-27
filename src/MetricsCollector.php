<?php

declare(strict_types=1);

namespace TomasVotruba\Lines;

use TomasVotruba\Lines\Enum\CounterName;

final class MetricsCollector
{
    /**
     * @var array<CounterName::*, mixed>
     */
    private array $counts = [];

    private int $currentClassLines = 0;

    private int $currentMethodLines = 0;

    private int $currentNumberOfMethods = 0;

    public function fetchResult(): MeasurementResult
    {
        return new MeasurementResult($this->counts);
    }

    public function addFile(string $filename): void
    {
        $this->increment(CounterName::FILES);
        $this->addUnique(CounterName::DIRECTORIES, dirname($filename));
    }

    public function incrementLines(int $number): void
    {
        $this->increment(CounterName::LINES, $number);
    }

    public function incrementCommentLines(int $number): void
    {
        $this->increment(CounterName::COMMENT_LINES, $number);
    }

    public function incrementLogicalLines(): void
    {
        $this->increment(CounterName::LOGICAL_LINES);
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
        $this->increment(CounterName::FUNCTION_LINES);
    }

    public function addConstant(string $name): void
    {
        $this->addToArray(CounterName::CONSTANT_NAMES, $name);
    }

    public function incrementNonStaticMethodCalls(): void
    {
        $this->increment(CounterName::NON_STATIC_METHOD_CALLS);
    }

    public function incrementStaticMethodCalls(): void
    {
        $this->increment(key: CounterName::STATIC_METHOD_CALLS);
    }

    public function addNamespace(string $namespace): void
    {
        $this->addUnique(CounterName::NAMESPACES, $namespace);
    }

    public function incrementInterfaces(): void
    {
        $this->increment(CounterName::INTERFACES);
    }

    public function incrementTraits(): void
    {
        $this->increment(CounterName::TRAITS);
    }

    public function incrementNonStaticMethods(): void
    {
        $this->increment(CounterName::NON_STATIC_METHODS);
    }

    public function incrementStaticMethods(): void
    {
        $this->increment(CounterName::STATIC_METHODS);
    }

    public function incrementPublicMethods(): void
    {
        $this->increment(CounterName::PUBLIC_METHODS);
    }

    public function incrementProtectedMethods(): void
    {
        $this->increment(CounterName::PROTECTED_METHODS);
    }

    public function incrementPrivateMethods(): void
    {
        $this->increment(CounterName::PRIVATE_METHODS);
    }

    public function incrementNamedFunctions(): void
    {
        $this->increment(CounterName::NAMED_FUNCTIONS);
    }

    public function incrementAnonymousFunctions(): void
    {
        $this->increment(CounterName::ANONYMOUS_FUNCTIONS);
    }

    public function incrementGlobalConstants(): void
    {
        $this->increment(CounterName::GLOBAL_CONSTANTS);
    }

    public function incrementPublicClassConstants(): void
    {
        $this->increment(CounterName::PUBLIC_CLASS_CONSTANTS);
    }

    public function incrementNonPublicClassConstants(): void
    {
        $this->increment(CounterName::NON_PUBLIC_CLASS_CONSTATNTS);
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

    /**
     * @param CounterName::* $key
     */
    private function increment(string $key, int $number = 1): void
    {
        $this->check($key, 0);
        $this->counts[$key] += $number;
    }

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
        $this->increment(CounterName::CLASSES);
    }
}
