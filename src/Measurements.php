<?php

declare (strict_types=1);
namespace Lines202412\TomasVotruba\Lines;

use Lines202412\TomasVotruba\Lines\Helpers\NumberFormat;
final class Measurements
{
    /**
     * @var string[]
     */
    private $directoryNames = [];
    /**
     * @var int
     */
    private $classCount = 0;
    /**
     * @var int
     */
    private $enumCount = 0;
    /**
     * @var int
     */
    private $traitCount = 0;
    /**
     * @var int
     */
    private $interfaceCount = 0;
    /**
     * @var int
     */
    private $lineCount = 0;
    /**
     * @var int
     */
    private $fileCount = 0;
    /**
     * @var int
     */
    private $nonStaticMethodCount = 0;
    /**
     * @var int
     */
    private $staticMethodCount = 0;
    /**
     * @var int
     */
    private $publicMethodCount = 0;
    /**
     * @var int
     */
    private $protectedMethodCount = 0;
    /**
     * @var int
     */
    private $privateMethodCount = 0;
    /**
     * @var int
     */
    private $functionCount = 0;
    /**
     * @var int
     */
    private $globalConstantCount = 0;
    /**
     * @var int
     */
    private $classConstantCount = 0;
    /**
     * @var int
     */
    private $commentLineCount = 0;
    /**
     * @var string[]
     */
    private $namespaceNames = [];
    /**
     * @var array<string, int>
     */
    private $filesToSize = [];
    public function addFile(string $filename) : void
    {
        $this->directoryNames[] = \dirname($filename);
        $relativeFilePath = \str_replace(\getcwd() . '/', '', $filename);
        $this->filesToSize[$relativeFilePath] = \substr_count((string) \file_get_contents($filename), "\n") + 1;
        ++$this->fileCount;
    }
    public function incrementLines(int $number) : void
    {
        $this->lineCount += $number;
    }
    public function incrementCommentLines(int $number) : void
    {
        $this->commentLineCount += $number;
    }
    public function addNamespace(string $namespace) : void
    {
        $this->namespaceNames[] = $namespace;
    }
    public function incrementInterfaceCount() : void
    {
        ++$this->interfaceCount;
    }
    public function incrementTraitCount() : void
    {
        ++$this->traitCount;
    }
    public function incrementNonStaticMethods() : void
    {
        ++$this->nonStaticMethodCount;
    }
    public function incrementStaticMethods() : void
    {
        ++$this->staticMethodCount;
    }
    public function incrementPublicMethods() : void
    {
        ++$this->publicMethodCount;
    }
    public function incrementProtectedMethods() : void
    {
        ++$this->protectedMethodCount;
    }
    public function incrementPrivateMethods() : void
    {
        ++$this->privateMethodCount;
    }
    public function incrementFunctionCount() : void
    {
        ++$this->functionCount;
    }
    public function incrementGlobalConstantCount() : void
    {
        ++$this->globalConstantCount;
    }
    public function incrementClassConstants(int $count) : void
    {
        $this->classConstantCount += $count;
    }
    public function incrementClassCount() : void
    {
        ++$this->classCount;
    }
    public function incrementEnumCount() : void
    {
        ++$this->enumCount;
    }
    public function getDirectoryCount() : int
    {
        $uniqueDirectoryNames = \array_unique($this->directoryNames);
        return \count($uniqueDirectoryNames) - 1;
    }
    public function getFileCount() : int
    {
        return $this->fileCount;
    }
    public function getLines() : int
    {
        return $this->lineCount;
    }
    public function getCommentLines() : int
    {
        return $this->commentLineCount;
    }
    public function getNonCommentLines() : int
    {
        return $this->lineCount - $this->commentLineCount;
    }
    public function getNamespaceCount() : int
    {
        $uniqueNamespaceNames = \array_unique($this->namespaceNames);
        return \count($uniqueNamespaceNames);
    }
    public function getInterfaceCount() : int
    {
        return $this->interfaceCount;
    }
    public function getTraitCount() : int
    {
        return $this->traitCount;
    }
    public function getClassCount() : int
    {
        return $this->classCount;
    }
    public function getMethodCount() : int
    {
        return $this->nonStaticMethodCount + $this->staticMethodCount;
    }
    public function getNonStaticMethods() : int
    {
        return $this->nonStaticMethodCount;
    }
    public function getStaticMethods() : int
    {
        return $this->staticMethodCount;
    }
    public function getPublicMethods() : int
    {
        return $this->publicMethodCount;
    }
    public function getProtectedMethods() : int
    {
        return $this->protectedMethodCount;
    }
    public function getPrivateMethods() : int
    {
        return $this->privateMethodCount;
    }
    public function getFunctionCount() : int
    {
        return $this->functionCount;
    }
    public function getGlobalConstantCount() : int
    {
        return $this->globalConstantCount;
    }
    public function getClassConstantCount() : int
    {
        return $this->classConstantCount;
    }
    public function getCommentLinesRelative() : float
    {
        if ($this->lineCount !== 0) {
            return $this->relative($this->commentLineCount, $this->lineCount);
        }
        return 0.0;
    }
    public function getNonCommentLinesRelative() : float
    {
        if ($this->lineCount !== 0) {
            return $this->relative($this->getNonCommentLines(), $this->lineCount);
        }
        return 0.0;
    }
    public function getStaticMethodsRelative() : float
    {
        if ($this->getMethodCount() > 0) {
            return $this->relative($this->staticMethodCount, $this->getMethodCount());
        }
        return 0.0;
    }
    public function getNonStaticMethodsRelative() : float
    {
        if ($this->getMethodCount() > 0) {
            return $this->relative($this->nonStaticMethodCount, $this->getMethodCount());
        }
        return 0.0;
    }
    public function getPublicMethodsRelative() : float
    {
        if ($this->getMethodCount() !== 0) {
            return $this->relative($this->publicMethodCount, $this->getMethodCount());
        }
        return 0.0;
    }
    public function getProtectedMethodsRelative() : float
    {
        if ($this->getMethodCount() !== 0) {
            return $this->relative($this->protectedMethodCount, $this->getMethodCount());
        }
        return 0.0;
    }
    public function getPrivateMethodsRelative() : float
    {
        if ($this->getMethodCount() !== 0) {
            return $this->relative($this->privateMethodCount, $this->getMethodCount());
        }
        return 0.0;
    }
    public function getEnumCount() : int
    {
        return $this->enumCount;
    }
    /**
     * @return array<string, int>
     */
    public function getLongestFiles() : array
    {
        // longest files first
        \arsort($this->filesToSize);
        // get top 10
        return \array_slice($this->filesToSize, 0, 10);
    }
    private function relative(int $partialNumber, int $totalNumber) : float
    {
        $relative = $partialNumber / $totalNumber * 100;
        return NumberFormat::singleDecimal($relative);
    }
}
