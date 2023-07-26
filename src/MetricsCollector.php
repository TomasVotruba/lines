<?php

declare(strict_types=1);

namespace TomasVotruba\Lines;

use TomasVotruba\Lines\Enum\CounterName;

final class MetricsCollector
{
    /**
     * @var array<string, mixed>
     */
    private array $counts = [];

    private int $currentClassLines = 0;

    private int $currentMethodLines = 0;

    private int $currentNumberOfMethods = 0;

    public function getPublisher(): Publisher
    {
        return new Publisher($this->counts);
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
        $this->addToArray('class lines', $this->currentClassLines);
        //}

        $this->currentClassLines = 0;
        $this->currentNumberOfMethods = 0;
    }

    public function currentClassStop(): void
    {
        $this->addToArray('methods per class', $this->currentNumberOfMethods);
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
        $this->addToArray('constant', $name);
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
        $this->addUnique('namespaces', $namespace);
    }

    public function incrementInterfaces(): void
    {
        $this->increment(CounterName::INTERFACES);
    }

    public function incrementTraits(): void
    {
        $this->increment('traits');
    }

    public function incrementAbstractClasses(): void
    {
        $this->increment(CounterName::ABSTRACT_CLASSES);
    }

    public function incrementNonFinalClasses(): void
    {
        $this->increment(CounterName::NON_FINAL_CLASSES);
    }

    public function incrementFinalClasses(): void
    {
        $this->increment(CounterName::FINAL_CLASSES);
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
        $this->increment('public methods');
    }

    public function incrementProtectedMethods(): void
    {
        $this->increment('protected methods');
    }

    public function incrementPrivateMethods(): void
    {
        $this->increment('private methods');
    }

    public function incrementNamedFunctions(): void
    {
        $this->increment('named functions');
    }

    public function incrementAnonymousFunctions(): void
    {
        $this->increment('anonymous functions');
    }

    public function incrementGlobalConstants(): void
    {
        $this->increment('global constants');
    }

    public function incrementPublicClassConstants(): void
    {
        $this->increment('public class constants');
    }

    public function incrementNonPublicClassConstants(): void
    {
        $this->increment('non-public class constants');
    }

    private function addUnique(string $key, mixed $name): void
    {
        $this->check($key, []);
        $this->counts[$key][$name] = true;
    }

    private function addToArray(string $key, mixed $value): void
    {
        $this->check($key, []);
        $this->counts[$key][] = $value;
    }

    private function increment(string $key, int $number = 1): void
    {
        $this->check($key, 0);
        $this->counts[$key] += $number;
    }

    /**
     * @param int|mixed[] $default
     */
    private function check(string $key, int|array $default): void
    {
        if (! isset($this->counts[$key])) {
            $this->counts[$key] = $default;
        }
    }
}
