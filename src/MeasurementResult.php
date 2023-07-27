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
        return $this->getCount('directories') - 1;
    }

    public function getFiles(): int
    {
        return $this->getValue('files');
    }

    public function getLines(): int
    {
        return $this->getValue('lines');
    }

    public function getCommentLines(): int
    {
        return $this->getValue('comment lines');
    }

    public function getNonCommentLines(): int
    {
        return $this->getLines() - $this->getCommentLines();
    }

    public function getLogicalLines(): int
    {
        return $this->getValue('logical lines');
    }

    public function getClassLines(): int
    {
        return $this->getSum('class lines');
    }

    public function getAverageClassLength(): float
    {
        return $this->getAverage('class lines');
    }

    public function getMinimumClassLength(): int
    {
        return $this->getMinimum('class lines');
    }

    public function getMaximumClassLength(): int
    {
        return $this->getMaximum('class lines');
    }

    public function getAverageMethodLength(): float
    {
        return $this->getAverage('method lines');
    }

    public function getMinimumMethodLength(): int
    {
        return $this->getMinimum('method lines');
    }

    public function getMaximumMethodLength(): int
    {
        return $this->getMaximum('method lines');
    }

    public function getAverageMethodsPerClass(): float
    {
        return $this->getAverage('methods per class');
    }

    public function getMinimumMethodsPerClass(): int
    {
        return $this->getMinimum('methods per class');
    }

    public function getMaximumMethodsPerClass(): int
    {
        return $this->getMaximum('methods per class');
    }

    public function getFunctionLines(): int
    {
        return $this->getValue('function lines');
    }

    public function getAverageFunctionLength(): float
    {
        return $this->divide($this->getFunctionLines(), $this->getFunctions());
    }

    public function getNotInClassesOrFunctions(): int
    {
        return $this->getLogicalLines() - $this->getClassLines() - $this->getFunctionLines();
    }

    //    public function getMethodCalls(): int
    //    {
    //        return $this->getNonStaticMethodCalls() + $this->getStaticMethodCalls();
    //    }

    //    public function getNonStaticMethodCalls(): int
    //    {
    //        return $this->getValue('non-static method calls');
    //    }
    //
    //    public function getStaticMethodCalls(): int
    //    {
    //        return $this->getValue('static method calls');
    //    }

    public function getNamespaces(): int
    {
        return $this->getCount('namespaces');
    }

    public function getInterfaces(): int
    {
        return $this->getValue('interfaces');
    }

    public function getTraits(): int
    {
        return $this->getValue('traits');
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
        return $this->getValue('non-static methods');
    }

    public function getStaticMethods(): int
    {
        return $this->getValue('static methods');
    }

    public function getPublicMethods(): int
    {
        return $this->getValue('public methods');
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
        return $this->getValue('protected methods');
    }

    public function getPrivateMethods(): int
    {
        return $this->getValue('private methods');
    }

    public function getFunctions(): int
    {
        return $this->getNamedFunctions() + $this->getAnonymousFunctions();
    }

    public function getNamedFunctions(): int
    {
        return $this->getValue('named functions');
    }

    public function getAnonymousFunctions(): int
    {
        return $this->getValue('anonymous functions');
    }

    public function getConstants(): int
    {
        return $this->getGlobalConstants() + $this->getClassConstants();
    }

    public function getGlobalConstants(): int
    {
        return $this->getValue('global constants');
    }

    public function getPublicClassConstants(): int
    {
        return $this->getValue('public class constants');
    }

    public function getNonPublicClassConstants(): int
    {
        return $this->getValue('non-public class constants');
    }

    public function getClassConstants(): int
    {
        return $this->getPublicClassConstants() + $this->getNonPublicClassConstants();
    }

    //        /**
    //         * @return array<string, mixed>
    //         */
    //        public function toArray(): array
    //        {
    //            return [
    //                'files' => $this->getFiles(),
    //                'loc' => $this->getLines(),
    //                'lloc' => $this->getLogicalLines(),
    //                'llocClasses' => $this->getClassLines(),
    //                'llocFunctions' => $this->getFunctionLines(),
    //                'llocGlobal' => $this->getNotInClassesOrFunctions(),
    //                'cloc' => $this->getCommentLines(),
    //                'interfaces' => $this->getInterfaces(),
    //                'traits' => $this->getTraits(),
    //                'classes' => $this->getClasses(),
    //                'functions' => $this->getFunctions(),
    //                'namedFunctions' => $this->getNamedFunctions(),
    //                'anonymousFunctions' => $this->getAnonymousFunctions(),
    //                'methods' => $this->getMethods(),
    //                'publicMethods' => $this->getPublicMethods(),
    //                'nonPublicMethods' => $this->getNonPublicMethods(),
    //                'protectedMethods' => $this->getProtectedMethods(),
    //                'privateMethods' => $this->getPrivateMethods(),
    //                'nonStaticMethods' => $this->getNonStaticMethods(),
    //                'staticMethods' => $this->getStaticMethods(),
    //                'constants' => $this->getConstants(),
    //                'classConstants' => $this->getClassConstants(),
    //                'publicClassConstants' => $this->getPublicClassConstants(),
    //                'nonPublicClassConstants' => $this->getNonPublicClassConstants(),
    //                'globalConstants' => $this->getGlobalConstants(),
    //                'llocByNof' => $this->getAverageFunctionLength(),
    //                'methodCalls' => $this->getMethodCalls(),
    //                'staticMethodCalls' => $this->getStaticMethodCalls(),
    //                'instanceMethodCalls' => $this->getNonStaticMethodCalls(),
    //                'directories' => $this->getDirectories(),
    //                'classLlocMin' => $this->getMinimumClassLength(),
    //                'classLlocAvg' => $this->getAverageClassLength(),
    //                'classLlocMax' => $this->getMaximumClassLength(),
    //                'methodLlocMin' => $this->getMinimumMethodLength(),
    //                'methodLlocAvg' => $this->getAverageMethodLength(),
    //                'methodLlocMax' => $this->getMaximumMethodLength(),
    //                'averageMethodsPerClass' => $this->getAverageMethodsPerClass(),
    //                'minimumMethodsPerClass' => $this->getMinimumMethodsPerClass(),
    //                'maximumMethodsPerClass' => $this->getMaximumMethodsPerClass(),
    //                'namespaces' => $this->getNamespaces(),
    //                'ncloc' => $this->getNonCommentLines(),
    //            ];
    //        }

    private function getAverage(string $key): float
    {
        $result = $this->divide($this->getSum($key), $this->getCount($key));
        return (float) number_format($result, 1);
    }

    private function getCount(string $key): int
    {
        return isset($this->counts[$key]) ? is_countable($this->counts[$key]) ? count($this->counts[$key]) : 0 : 0;
    }

    private function getSum(string $key): int
    {
        if (! isset($this->counts[$key])) {
            return 0;
        }

        return (int) array_sum($this->counts[$key]);
    }

    private function getMaximum(string $key): int
    {
        return isset($this->counts[$key]) ? max($this->counts[$key]) : 0;
    }

    private function getMinimum(string $key): int
    {
        return isset($this->counts[$key]) ? min($this->counts[$key]) : 0;
    }

    private function getValue(string $key): mixed
    {
        return $this->counts[$key] ?? 0;
    }

    private function divide(int $x, int $y): float
    {
        return $y != 0 ? $x / $y : 0;
    }
}
