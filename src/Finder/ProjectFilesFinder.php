<?php

declare (strict_types=1);
namespace Lines202512\TomasVotruba\Lines\Finder;

use Lines202512\Symfony\Component\Finder\Finder;
use Lines202512\Symfony\Component\Finder\SplFileInfo;
final class ProjectFilesFinder
{
    /**
     * @return SplFileInfo[]
     */
    public function find(string $projectDirectory) : array
    {
        $finder = Finder::create()->name('*.php')->in($projectDirectory)->notPath('vendor')->notPath('stubs')->notPath('bin')->notPath('migrations')->notPath('data-fixtures')->notPath('build');
        /** @var SplFileInfo[] $fileInfos */
        $fileInfos = \iterator_to_array($finder->getIterator());
        return $fileInfos;
    }
}
