<?php

declare(strict_types=1);

namespace TomasVotruba\Lines\Finder;

use Symfony\Component\Finder\Finder;
use Webmozart\Assert\Assert;

final class PhpFilesFinder
{
    /**
     * @param string[] $directories
     * @param string[] $exclude
     * @return string[]
     */
    public function findInDirectories(array $directories, array $exclude = []): array
    {
        $phpFilesFinder = Finder::create()
            ->files()
            ->in($directories)
            ->name('*.php')
            ->exclude($exclude);

        // skip yourself
        $phpFilesFinder->notPath('tomasvotruba/lines');

        $filePaths = [];
        foreach ($phpFilesFinder->getIterator() as $fileInfo) {
            $filePaths[] = $fileInfo->getRealPath();
        }

        Assert::allString($filePaths);

        return $filePaths;
    }
}
