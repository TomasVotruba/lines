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
        $measurements = $this->analyser->measureFiles([__DIR__ . '/Fixture/source.php']);

        $this->assertSame(1, $measurements->getFiles());
        $this->assertSame(82, $measurements->getLines());
        $this->assertSame(30, $measurements->getLogicalLines());
        $this->assertSame(28, $measurements->getClassLines());
        $this->assertSame(1, $measurements->getFunctionLines());
        $this->assertSame(1, $measurements->getNotInClassesOrFunctions());
        $this->assertSame(7, $measurements->getCommentLines());
        $this->assertSame(1, $measurements->getInterfaces());
        $this->assertSame(0, $measurements->getTraits());
        $this->assertSame(2, $measurements->getClasses());
        $this->assertSame(2, $measurements->getFunctions());
        $this->assertSame(1, $measurements->getNamedFunctions());
        $this->assertSame(1, $measurements->getAnonymousFunctions());
        $this->assertSame(4, $measurements->getMethods());
        $this->assertSame(2, $measurements->getPublicMethods());
        $this->assertSame(2, $measurements->getNonPublicMethods());
        $this->assertSame(1, $measurements->getProtectedMethods());
        $this->assertSame(1, $measurements->getPrivateMethods());
        $this->assertSame(3, $measurements->getNonStaticMethods());
        $this->assertSame(1, $measurements->getStaticMethods());
        $this->assertSame(2, $measurements->getConstants());
        $this->assertSame(1, $measurements->getClassConstants());
        $this->assertSame(1, $measurements->getPublicClassConstants());
        $this->assertSame(0, $measurements->getNonPublicClassConstants());
        $this->assertSame(1, $measurements->getGlobalConstants());
        $this->assertSame(0.5, $measurements->getAverageFunctionLength());
        $this->assertSame(0, $measurements->getDirectories());
        $this->assertSame(1, $measurements->getNamespaces());
        $this->assertSame(75, $measurements->getNonCommentLines());
        $this->assertSame(0, $measurements->getMinimumClassLength());
        $this->assertSame(4.0, $measurements->getAverageClassLength());
        $this->assertSame(28, $measurements->getMaximumClassLength());
        $this->assertSame(6, $measurements->getMinimumMethodLength());
        $this->assertSame(7.3, $measurements->getAverageMethodLength());
        $this->assertSame(9, $measurements->getMaximumMethodLength());
        $this->assertSame(1.3, $measurements->getAverageMethodsPerClass());
        $this->assertSame(0, $measurements->getMinimumMethodsPerClass());
        $this->assertSame(4, $measurements->getMaximumMethodsPerClass());
    }

    #[DataProvider('issue126Provider')]
    public function testIssue126IsFixed(int $fileNumber, int $expectedCommentLines): void
    {
        $measurements = $this->analyser->measureFiles([
            __DIR__ . '/Fixture/issue_126/issue_126_' . $fileNumber . '.php',
        ]);

        $this->assertSame($expectedCommentLines, $measurements->getCommentLines());
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
        $measurements = $this->analyser->measureFiles([__DIR__ . '/Fixture/issue_138.php']);
        $this->assertSame(1, $measurements->getClasses());
    }

    public function testDeclareIsNotLogicalLine(): void
    {
        $measurements = $this->analyser->measureFiles([__DIR__ . '/Fixture/with_declare.php']);
        $this->assertSame(0, $measurements->getNotInClassesOrFunctions());
    }

    public function testNamespaceIsNotLogicalLine(): void
    {
        $measurements = $this->analyser->measureFiles([__DIR__ . '/Fixture/with_namespace.php']);
        $this->assertSame(0, $measurements->getNotInClassesOrFunctions());
    }

    public function testImportIsNotLogicalLine(): void
    {
        $measurements = $this->analyser->measureFiles([__DIR__ . '/Fixture/with_import.php']);
        $this->assertSame(0, $measurements->getNotInClassesOrFunctions());
    }

    public function testConstAndPublicClassConst(): void
    {
        $measurements = $this->analyser->measureFiles([__DIR__ . '/Fixture/class_constants.php']);

        $this->assertSame(2, $measurements->getPublicClassConstants());
        $this->assertSame(3, $measurements->getNonPublicClassConstants());

        $this->assertSame(5, $measurements->getClassConstants());
        $this->assertSame(5, $measurements->getConstants());
    }

    public function testClasses(): void
    {
        $measurements = $this->analyser->measureFiles([__DIR__ . '/Fixture/classes.php']);
        $this->assertSame(9, $measurements->getClasses());
    }

    public function testMethodVisibility(): void
    {
        $measurements = $this->analyser->measureFiles([__DIR__ . '/Fixture/methods.php']);

        $this->assertSame(2, $measurements->getPublicMethods());
        $this->assertSame(1, $measurements->getProtectedMethods());
        $this->assertSame(3, $measurements->getPrivateMethods());
        $this->assertSame(6, $measurements->getMethods());
    }

    public function testAverageMethodsPerClass(): void
    {
        $measurements = $this->analyser->measureFiles([__DIR__ . '/Fixture/methods_per_class.php']);

        $this->assertSame(2.0, $measurements->getAverageMethodsPerClass());

        $this->assertSame(0, $measurements->getMinimumMethodsPerClass());
        $this->assertSame(4, $measurements->getMaximumMethodsPerClass());
    }

    public function testSkipTraitFromLogicalLines(): void
    {
        $measurements = $this->analyser->measureFiles([__DIR__ . '/Fixture/class_using_trait.php']);
        $this->assertSame(1, $measurements->getClassLines());
    }
}
