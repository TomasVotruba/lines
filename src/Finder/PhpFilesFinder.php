<?php

declare(strict_types=1);

namespace TomasVotruba\Lines\Finder;

use SplFileInfo;
use Symfony\Component\Finder\Finder;
use Webmozart\Assert\Assert;

final class PhpFilesFinder
{
    /**
     * @param string[] $paths
     * @param string[] $excludes
     *
     * @return string[]
     */
    public function findInDirectories(array $paths, array $excludes = [], bool $allowVendor = false): array
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

        // only files
        if ($directories === []) {
            return $filePaths;
        }

        $phpFilesFinder = Finder::create()
            ->files()
            ->in($directories)
            ->sortByName()
            ->name('*.php')
            ->notPath('tomasvotruba/lines')
            // fix exclude to handle directories
            ->filter(function (SplFileInfo $fileInfo) use ($excludes) {
                foreach ($excludes as $exclude) {
                    if (str_contains($fileInfo->getRealPath(), $exclude)) {
                        return \false;
                    }
                }

                return true;
            });

        if ($allowVendor === false) {
            // skip vendor directory, as we often need the full source code
            $phpFilesFinder->notPath('vendor');
        }

        return $this->resolveRealPaths($phpFilesFinder);
    }

    /**
     * @return string[]
     */
    private function resolveRealPaths(Finder $finder): array
    {
        $realFilePaths = [];

        foreach ($finder->getIterator() as $fileInfo) {
            $realFilePaths[] = $fileInfo->getRealPath();
        }

        Assert::allString($realFilePaths);

        return $realFilePaths;
    }
}
