<?php

declare(strict_types=1);

namespace TomasVotruba\Lines;

final class Collector
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
        $this->increment('files');
        $this->addUnique('directories', dirname((string) $filename));
    }

    public function incrementLines(int $number): void
    {
        $this->increment('lines', $number);
    }

    public function incrementCommentLines(int $number): void
    {
        $this->increment('comment lines', $number);
    }

    public function incrementLogicalLines(): void
    {
        $this->increment('logical lines');
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
        $this->addToArray('method lines', $this->currentMethodLines);
    }

    public function incrementFunctionLines(): void
    {
        $this->increment('function lines');
    }

    public function addConstant(string $name): void
    {
        $this->addToArray('constant', $name);
    }

    public function incrementNonStaticMethodCalls(): void
    {
        $this->increment('non-static method calls');
    }

    public function incrementStaticMethodCalls(): void
    {
        $this->increment('static method calls');
    }

    public function addNamespace(string $namespace): void
    {
        $this->addUnique('namespaces', $namespace);
    }

    public function incrementInterfaces(): void
    {
        $this->increment('interfaces');
    }

    public function incrementTraits(): void
    {
        $this->increment('traits');
    }

    public function incrementAbstractClasses(): void
    {
        $this->increment('abstract classes');
    }

    public function incrementNonFinalClasses(): void
    {
        $this->increment('non-final classes');
    }

    public function incrementFinalClasses(): void
    {
        $this->increment('final classes');
    }

    public function incrementNonStaticMethods(): void
    {
        $this->increment('non-static methods');
    }

    public function incrementStaticMethods(): void
    {
        $this->increment('static methods');
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

    private function check(string $key, mixed $default): void
    {
        if (! isset($this->counts[$key])) {
            $this->counts[$key] = $default;
        }
    }
}
