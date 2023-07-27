<?php

declare(strict_types=1);

namespace TomasVotruba\Lines\Tests;

use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;
use TomasVotruba\Lines\Analyser;

final class AnalyserTest extends TestCase
{
    private Analyser $analyser;

    protected function setUp(): void
    {
        $this->analyser = new Analyser();
    }

    public function test(): void
    {
        $this->assertEqualsWithDelta(
            [
                'files' => 1,
                'loc' => 82,
                'lloc' => 30,
                'llocClasses' => 28,
                'llocFunctions' => 1,
                'llocGlobal' => 1,
                'expectedCommentLines' => 7,
                'interfaces' => 1,
                'traits' => 0,
                'classes' => 2,
                'functions' => 2,
                'namedFunctions' => 1,
                'anonymousFunctions' => 1,
                'methods' => 4,
                'publicMethods' => 2,
                'nonPublicMethods' => 2,
                'protectedMethods' => 1,
                'privateMethods' => 1,
                'nonStaticMethods' => 3,
                'staticMethods' => 1,
                'constants' => 2,
                'classConstants' => 1,
                'publicClassConstants' => 1,
                'nonPublicClassConstants' => 0,
                'globalConstants' => 1,
                'llocByNof' => 0.5,
                'methodCalls' => 6,
                'staticMethodCalls' => 4,
                'instanceMethodCalls' => 2,
                'directories' => 0,
                'namespaces' => 1,
                'ncloc' => 75,
                'classLlocMin' => 0,
                'classLlocAvg' => 4.0,
                'classLlocMax' => 28,
                'methodLlocMin' => 6,
                'methodLlocAvg' => 7.3,
                'methodLlocMax' => 9,
                'averageMethodsPerClass' => 1.3,
                'minimumMethodsPerClass' => 0,
                'maximumMethodsPerClass' => 4,
            ],
            $this->analyser->countFiles([__DIR__ . '/Fixture/source.php']),
            0.1
        );
    }

    #[DataProvider('issue126Provider')]
    public function testIssue126IsFixed(int $fileNumber, int $expectedCommentLines): void
    {
        $measurementResult = $this->analyser->countFiles([
            __DIR__ . '/Fixture/issue_126/issue_126_' . $fileNumber . '.php',
        ]);

        $this->assertSame($expectedCommentLines, $measurementResult->getCommentLines());
    }

    /**
     * @return array<array{int, int}>
     */
    public static function issue126Provider(): array
    {
        return [[1, 1], [2, 1], [3, 1], [4, 2], [5, 3], [6, 3], [7, 3]];
    }

    public function testIssue138IsFixed(): void
    {
        $measurementResult = $this->analyser->countFiles([__DIR__ . '/Fixture/issue_138.php']);
        $this->assertSame(1, $measurementResult->getClasses());
    }

    public function testDeclareIsNotLogicalLine(): void
    {
        $measurementResult = $this->analyser->countFiles([__DIR__ . '/Fixture/with_declare.php']);
        $this->assertSame(0, $measurementResult->getNotInClassesOrFunctions());
    }

    public function testNamespaceIsNotLogicalLine(): void
    {
        $measurementResult = $this->analyser->countFiles([__DIR__ . '/Fixture/with_namespace.php']);
        $this->assertSame(0, $measurementResult->getNotInClassesOrFunctions());
    }

    public function testImportIsNotLogicalLine(): void
    {
        $measurementResult = $this->analyser->countFiles([__DIR__ . '/Fixture/with_import.php']);
        $this->assertSame(0, $measurementResult->getNotInClassesOrFunctions());
    }

    public function test_it_makes_a_distinction_between_public_and_non_public_class_constants(): void
    {
        $measurementResult = $this->analyser->countFiles([__DIR__ . '/Fixture/class_constants.php']);

        $this->assertSame(2, $measurementResult->getPublicClassConstants());
        $this->assertSame(3, $measurementResult->getNonPublicClassConstants());

        $this->assertSame(5, $measurementResult->getClassConstants());
        $this->assertSame(5, $measurementResult->getConstants());
    }

    public function test_it_collects_the_number_of_final_non_final_and_abstract_classes(): void
    {
        $measurementResult = $this->analyser->countFiles([__DIR__ . '/Fixture/classes.php']);
        $this->assertSame(9, $measurementResult->getClasses());
    }

    public function test_it_makes_a_distinction_between_protected_and_private_methods(): void
    {
        $measurementResult = $this->analyser->countFiles([__DIR__ . '/Fixture/methods.php']);

        $this->assertSame(2, $measurementResult->getPublicMethods());
        $this->assertSame(1, $measurementResult->getProtectedMethods());
        $this->assertSame(3, $measurementResult->getPrivateMethods());
        $this->assertSame(6, $measurementResult->getMethods());
    }

    public function test_it_provides_average_minimum_and_maximum_number_of_methods_per_class(): void
    {
        $measurementResult = $this->analyser->countFiles([__DIR__ . '/Fixture/methods_per_class.php']);

        $this->assertSame(2.0, $measurementResult->getAverageMethodsPerClass());

        $this->assertSame(0, $measurementResult->getMinimumMethodsPerClass());
        $this->assertSame(4, $measurementResult->getMaximumMethodsPerClass());
    }

    public function test_use_trait_is_not_counted_as_logical_line(): void
    {
        $measurementResult = $this->analyser->countFiles([__DIR__ . '/Fixture/class_using_trait.php']);
        $this->assertSame(1, $measurementResult->getClassLines());
    }
}
