<?php

declare(strict_types=1);

namespace TomasVotruba\Lines\Finder;

use SebastianBergmann\FileIterator\Facade;

final class PhpFilesFinder
{
    /**
     * @param string[] $directories
     * @param string[] $suffixes
     * @param string[] $exclude
     * @return string[]
     */
    public function findInDirectories(array $directories, array $suffixes, array $exclude): array
    {
        $filePaths = [];
        $facade = new Facade();

        foreach ($directories as $directory) {
            $currentFilePaths = $facade->getFilesAsArray($directory, $suffixes, '', $exclude);
            $filePaths = [...$filePaths, ...$currentFilePaths];
        }

        return $filePaths;
    }
}
