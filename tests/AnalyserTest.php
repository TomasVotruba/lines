<?php

declare(strict_types=1);

namespace TomasVotruba\Lines\Tests;

use PHPUnit\Framework\TestCase;
use TomasVotruba\Lines\Analyser;
use TomasVotruba\Lines\DependencyInjection\ContainerFactory;

final class AnalyserTest extends TestCase
{
    private Analyser $analyser;

    protected function setUp(): void
    {
        $containerFactory = new ContainerFactory();
        $container = $containerFactory->create();

        $this->analyser = $container->make(Analyser::class);
    }

    public function test(): void
    {
        $measurements = $this->analyser->measureFiles([__DIR__ . '/Fixture/source.php']);

        $this->assertSame(0, $measurements->getDirectoryCount());
        $this->assertSame(1, $measurements->getFileCount());

        // lines
        $this->assertSame(82, $measurements->getLines());
        $this->assertSame(75, $measurements->getNonCommentLines());
        $this->assertSame(7, $measurements->getCommentLines());

        // structure
        $this->assertSame(1, $measurements->getNamespaceCount());
        $this->assertSame(2, $measurements->getClassCount());
        $this->assertSame(4, $measurements->getMethodCount());
        $this->assertSame(1, $measurements->getClassConstantCount());
        $this->assertSame(1, $measurements->getInterfaceCount());
        $this->assertSame(0, $measurements->getTraitCount());
        $this->assertSame(1, $measurements->getFunctionCount());
        $this->assertSame(1, $measurements->getClosureCount());
        $this->assertSame(1, $measurements->getGlobalConstantCount());

        // methods
        $this->assertSame(2, $measurements->getPublicMethods());
        $this->assertSame(1, $measurements->getProtectedMethods());
        $this->assertSame(1, $measurements->getPrivateMethods());

        // static and non-static
        $this->assertEqualsWithDelta(25.0, $measurements->getStaticMethodsRelative(), PHP_FLOAT_EPSILON);
        $this->assertSame(1, $measurements->getStaticMethods());
        $this->assertSame(3, $measurements->getNonStaticMethods());
        $this->assertEqualsWithDelta(75.0, $measurements->getNonStaticMethodsRelative(), PHP_FLOAT_EPSILON);

        // relative
        $this->assertEqualsWithDelta(8.5, $measurements->getCommentLinesRelative(), PHP_FLOAT_EPSILON);
        $this->assertEqualsWithDelta(91.5, $measurements->getNonCommentLinesRelative(), PHP_FLOAT_EPSILON);
    }

    public function testSkipAnonymousClass(): void
    {
        $measurements = $this->analyser->measureFiles([__DIR__ . '/Fixture/issue_138.php']);
        $this->assertSame(1, $measurements->getClassCount());
    }

    public function testConstAndPublicClassConst(): void
    {
        $measurements = $this->analyser->measureFiles([__DIR__ . '/Fixture/class_constants.php']);

        $this->assertSame(5, $measurements->getClassConstantCount());
        $this->assertSame(1, $measurements->getGlobalConstantCount());
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

    public function testEnums(): void
    {
        $measurements = $this->analyser->measureFiles([__DIR__ . '/Fixture/enums.php']);

        $this->assertSame(0, $measurements->getClassCount());
        $this->assertSame(1, $measurements->getEnumCount());
    }
}
