<?php

declare (strict_types=1);
namespace Lines202308\TomasVotruba\Lines\Finder;

use Lines202308\Symfony\Component\Finder\Finder;
use Lines202308\Webmozart\Assert\Assert;
final class PhpFilesFinder
{
    /**
     * @param string[] $paths
     * @param string[] $exclude
     * @return string[]
     */
    public function findInDirectories(array $paths, array $exclude = []) : array
    {
        Assert::allFileExists($paths);
        $filePaths = [];
        $directories = [];
        foreach ($paths as $path) {
            if (\is_file($path)) {
                $filePaths[] = $path;
            } else {
                $directories[] = $path;
            }
        }
        if ($directories !== []) {
            $phpFilesFinder = Finder::create()->files()->in($directories)->name('*.php')->notPath('tomasvotruba/lines')->exclude($exclude);
            foreach ($phpFilesFinder->getIterator() as $fileInfo) {
                $filePaths[] = $fileInfo->getRealPath();
            }
        }
        Assert::allString($filePaths);
        return $filePaths;
    }
}
