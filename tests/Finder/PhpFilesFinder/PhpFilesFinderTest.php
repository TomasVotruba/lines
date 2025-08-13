<?php

declare(strict_types=1);

namespace Finder\PhpFilesFinder;

use PHPUnit\Framework\TestCase;
use TomasVotruba\Lines\Finder\PhpFilesFinder;

final class PhpFilesFinderTest extends TestCase
{
    public function test(): void
    {
        $phpFilesFinder = new PhpFilesFinder();
        $phpFiles = $phpFilesFinder->findInDirectories([__DIR__ . '/Fixture'], ['Fixture/ExcludeMe']);

        $this->assertCount(1, $phpFiles);
    }
}
