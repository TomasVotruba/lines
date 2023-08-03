<?php

declare(strict_types=1);

namespace TomasVotruba\Lines\Finder;

use Symfony\Component\Finder\Finder;
use Webmozart\Assert\Assert;

final class PhpFilesFinder
{
    /**
     * @param string[] $paths
     * @param string[] $exclude
     * @return string[]
     */
    public function findInDirectories(array $paths, array $exclude = []): array
    {
        Assert::allFileExists($paths);

        $filePaths = [];
        $directories = [];

        foreach ($paths as $path) {
            if (is_file($path)) {
                $filePaths[] = $path;
            } else {
                $directories[] = $path;
            }
        }

        if ($directories !== []) {
            $phpFilesFinder = Finder::create()
                ->files()
                ->in($directories)
                ->name('*.php')
                // skip this package in /vendor
                ->notPath('tomasvotruba/lines')
                ->exclude($exclude);

            foreach ($phpFilesFinder->getIterator() as $fileInfo) {
                $filePaths[] = $fileInfo->getRealPath();
            }
        }

        Assert::allString($filePaths);

        return $filePaths;
    }
}
