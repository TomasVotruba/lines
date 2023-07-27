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
        $measurementResult = $this->analyser->measureFiles([__DIR__ . '/Fixture/source.php']);

        $this->assertSame(1, $measurementResult->getFiles());
        $this->assertSame(82, $measurementResult->getLines());
        $this->assertSame(30, $measurementResult->getLogicalLines());
        $this->assertSame(28, $measurementResult->getClassLines());
        $this->assertSame(1, $measurementResult->getFunctionLines());
        $this->assertSame(1, $measurementResult->getNotInClassesOrFunctions());
        $this->assertSame(7, $measurementResult->getCommentLines());
        $this->assertSame(1, $measurementResult->getInterfaces());
        $this->assertSame(0, $measurementResult->getTraits());
        $this->assertSame(2, $measurementResult->getClasses());
        $this->assertSame(2, $measurementResult->getFunctions());
        $this->assertSame(1, $measurementResult->getNamedFunctions());
        $this->assertSame(1, $measurementResult->getAnonymousFunctions());
        $this->assertSame(4, $measurementResult->getMethods());
        $this->assertSame(2, $measurementResult->getPublicMethods());
        $this->assertSame(2, $measurementResult->getNonPublicMethods());
        $this->assertSame(1, $measurementResult->getProtectedMethods());
        $this->assertSame(1, $measurementResult->getPrivateMethods());
        $this->assertSame(3, $measurementResult->getNonStaticMethods());
        $this->assertSame(1, $measurementResult->getStaticMethods());
        $this->assertSame(2, $measurementResult->getConstants());
        $this->assertSame(1, $measurementResult->getClassConstants());
        $this->assertSame(1, $measurementResult->getPublicClassConstants());
        $this->assertSame(0, $measurementResult->getNonPublicClassConstants());
        $this->assertSame(1, $measurementResult->getGlobalConstants());
        $this->assertSame(0.5, $measurementResult->getAverageFunctionLength());
        $this->assertSame(0, $measurementResult->getDirectories());
        $this->assertSame(1, $measurementResult->getNamespaces());
        $this->assertSame(75, $measurementResult->getNonCommentLines());
        $this->assertSame(0, $measurementResult->getMinimumClassLength());
        $this->assertSame(4.0, $measurementResult->getAverageClassLength());
        $this->assertSame(28, $measurementResult->getMaximumClassLength());
        $this->assertSame(6, $measurementResult->getMinimumMethodLength());
        $this->assertSame(7.3, $measurementResult->getAverageMethodLength());
        $this->assertSame(9, $measurementResult->getMaximumMethodLength());
        $this->assertSame(1.3, $measurementResult->getAverageMethodsPerClass());
        $this->assertSame(0, $measurementResult->getMinimumMethodsPerClass());
        $this->assertSame(4, $measurementResult->getMaximumMethodsPerClass());
    }

    #[DataProvider('issue126Provider')]
    public function testIssue126IsFixed(int $fileNumber, int $expectedCommentLines): void
    {
        $measurementResult = $this->analyser->measureFiles([
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

    public function testSkipAnonymousClass(): void
    {
        $measurementResult = $this->analyser->measureFiles([__DIR__ . '/Fixture/issue_138.php']);
        $this->assertSame(1, $measurementResult->getClasses());
    }

    public function testDeclareIsNotLogicalLine(): void
    {
        $measurementResult = $this->analyser->measureFiles([__DIR__ . '/Fixture/with_declare.php']);
        $this->assertSame(0, $measurementResult->getNotInClassesOrFunctions());
    }

    public function testNamespaceIsNotLogicalLine(): void
    {
        $measurementResult = $this->analyser->measureFiles([__DIR__ . '/Fixture/with_namespace.php']);
        $this->assertSame(0, $measurementResult->getNotInClassesOrFunctions());
    }

    public function testImportIsNotLogicalLine(): void
    {
        $measurementResult = $this->analyser->measureFiles([__DIR__ . '/Fixture/with_import.php']);
        $this->assertSame(0, $measurementResult->getNotInClassesOrFunctions());
    }

    public function testConstAndPublicClassConst(): void
    {
        $measurementResult = $this->analyser->measureFiles([__DIR__ . '/Fixture/class_constants.php']);

        $this->assertSame(2, $measurementResult->getPublicClassConstants());
        $this->assertSame(3, $measurementResult->getNonPublicClassConstants());

        $this->assertSame(5, $measurementResult->getClassConstants());
        $this->assertSame(5, $measurementResult->getConstants());
    }

    public function testClasses(): void
    {
        $measurementResult = $this->analyser->measureFiles([__DIR__ . '/Fixture/classes.php']);
        $this->assertSame(9, $measurementResult->getClasses());
    }

    public function testMethodVisibility(): void
    {
        $measurementResult = $this->analyser->measureFiles([__DIR__ . '/Fixture/methods.php']);

        $this->assertSame(2, $measurementResult->getPublicMethods());
        $this->assertSame(1, $measurementResult->getProtectedMethods());
        $this->assertSame(3, $measurementResult->getPrivateMethods());
        $this->assertSame(6, $measurementResult->getMethods());
    }

    public function testAverageMethodsPerClass(): void
    {
        $measurementResult = $this->analyser->measureFiles([__DIR__ . '/Fixture/methods_per_class.php']);

        $this->assertSame(2.0, $measurementResult->getAverageMethodsPerClass());

        $this->assertSame(0, $measurementResult->getMinimumMethodsPerClass());
        $this->assertSame(4, $measurementResult->getMaximumMethodsPerClass());
    }

    public function testSkipTraitFromLogicalLines(): void
    {
        $measurementResult = $this->analyser->measureFiles([__DIR__ . '/Fixture/class_using_trait.php']);
        $this->assertSame(1, $measurementResult->getClassLines());
    }
}
