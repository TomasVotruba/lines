<?php

declare(strict_types=1);

namespace TomasVotruba\Lines\Tests;

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
        $this->assertSame(1, $measurements->getInterfaceCount());
        $this->assertSame(0, $measurements->getTraitCount());
        $this->assertSame(2, $measurements->getClassCount());
        $this->assertSame(2, $measurements->getFunctionCount());
        $this->assertSame(4, $measurements->getMethodCount());
        $this->assertSame(2, $measurements->getPublicMethods());
        $this->assertSame(1, $measurements->getProtectedMethods());
        $this->assertSame(1, $measurements->getPrivateMethods());
        $this->assertSame(3, $measurements->getNonStaticMethods());
        $this->assertSame(1, $measurements->getStaticMethods());
        $this->assertSame(2, $measurements->getConstantCount());
        $this->assertSame(1, $measurements->getClassConstants());
        $this->assertSame(1, $measurements->getPublicClassConstants());
        $this->assertSame(0, $measurements->getNonPublicClassConstants());
        $this->assertSame(1, $measurements->getGlobalConstantCount());
        $this->assertSame(0, $measurements->getDirectories());
        $this->assertSame(1, $measurements->getNamespaces());
        $this->assertSame(75, $measurements->getNonCommentLines());

        $this->assertSame(28, $measurements->getMaxClassLength());
        $this->assertSame(9, $measurements->getMaxMethodLength());

        // average
        $this->assertSame(4.0, $measurements->getAverageClassLength());
        $this->assertSame(7.3, $measurements->getAverageMethodLength());

        // relative
        $this->assertSame(8.5, $measurements->getCommentLinesRelative());
        $this->assertSame(3.3, $measurements->getFunctionLinesRelative());
        $this->assertSame(93.3, $measurements->getClassLinesRelative());
        $this->assertSame(91.5, $measurements->getNonCommentLinesRelative());
        $this->assertSame(3.3, $measurements->getNotInClassesOrFunctionsRelative());

        $this->assertSame(25.0, $measurements->getStaticMethodsRelative());
        $this->assertSame(75.0, $measurements->getNonStaticMethodsRelative());
    }

    public function testSkipAnonymousClass(): void
    {
        $measurements = $this->analyser->measureFiles([__DIR__ . '/Fixture/issue_138.php']);
        $this->assertSame(1, $measurements->getClassCount());
    }

    public function testNamespaceIsNotLogicalLine(): void
    {
        $measurements = $this->analyser->measureFiles(
            [__DIR__ . '/Fixture/with_namespace.php', __DIR__ . '/Fixture/with_declare.php']
        );
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
        $this->assertSame(5, $measurements->getConstantCount());
    }

    public function testClasses(): void
    {
        $measurements = $this->analyser->measureFiles([__DIR__ . '/Fixture/classes.php']);
        $this->assertSame(9, $measurements->getClassCount());
    }

    public function testMethodVisibility(): void
    {
        $measurements = $this->analyser->measureFiles([__DIR__ . '/Fixture/methods.php']);

        $this->assertSame(2, $measurements->getPublicMethods());
        $this->assertSame(1, $measurements->getProtectedMethods());
        $this->assertSame(3, $measurements->getPrivateMethods());
        $this->assertSame(6, $measurements->getMethodCount());
    }

    public function testSkipTraitFromLogicalLines(): void
    {
        $measurements = $this->analyser->measureFiles([__DIR__ . '/Fixture/class_using_trait.php']);
        $this->assertSame(1, $measurements->getClassLines());
    }

    public function testEnums(): void
    {
        $measurements = $this->analyser->measureFiles([__DIR__ . '/Fixture/enums.php']);

        $this->assertSame(0, $measurements->getClassCount());
        $this->assertSame(1, $measurements->getEnumCount());
    }
}
