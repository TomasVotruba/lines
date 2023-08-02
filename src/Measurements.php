<?php

declare (strict_types=1);
namespace Lines202308\TomasVotruba\Lines;

use Lines202308\TomasVotruba\Lines\Helpers\NumberFormat;
final class Measurements
{
    /**
     * @var int[]
     */
    private $classLineCountPerClass = [];
    /**
     * @var int[]
     */
    private $methodLineCountPerMethod = [];
    /**
     * @var int
     */
    private $currentClassLines = 0;
    /**
     * @var int
     */
    private $currentMethodLines = 0;
    /**
     * @var int
     */
    private $currentClassMethodCount = 0;
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
    private $publicClassConstantCount = 0;
    /**
     * @var int
     */
    private $nonPublicClassConstantCount = 0;
    /**
     * @var int
     */
    private $logicalLineCount = 0;
    /**
     * @var int
     */
    private $commentLineCount = 0;
    /**
     * @var int
     */
    private $functionLineCount = 0;
    /**
     * @var string[]
     */
    private $namespaceNames = [];
    public function addFile(string $filename) : void
    {
        $this->directoryNames[] = \dirname($filename);
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
    public function incrementLogicalLines() : void
    {
        ++$this->logicalLineCount;
    }
    public function resetCurrentClass() : void
    {
        $this->classLineCountPerClass[] = $this->currentClassLines;
        $this->currentClassLines = 0;
        $this->currentClassMethodCount = 0;
    }
    public function incrementCurrentClassLines() : void
    {
        ++$this->currentClassLines;
    }
    public function currentMethodStart() : void
    {
        $this->currentMethodLines = 0;
    }
    public function currentClassIncrementMethods() : void
    {
        ++$this->currentClassMethodCount;
    }
    public function currentMethodIncrementLines() : void
    {
        ++$this->currentMethodLines;
    }
    public function currentMethodStop() : void
    {
        $this->methodLineCountPerMethod[] = $this->currentMethodLines;
    }
    public function incrementFunctionLines() : void
    {
        ++$this->functionLineCount;
    }
    public function addNamespace(string $namespace) : void
    {
        $this->namespaceNames[] = $namespace;
    }
    public function incrementInterfaces() : void
    {
        ++$this->interfaceCount;
    }
    public function incrementTraits() : void
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
    public function incrementFunctions() : void
    {
        ++$this->functionCount;
    }
    public function incrementGlobalConstants() : void
    {
        ++$this->globalConstantCount;
    }
    public function incrementPublicClassConstants() : void
    {
        ++$this->publicClassConstantCount;
    }
    public function incrementNonPublicClassConstants() : void
    {
        ++$this->nonPublicClassConstantCount;
    }
    public function incrementClasses() : void
    {
        ++$this->classCount;
    }
    public function incrementEnums() : void
    {
        ++$this->enumCount;
    }
    public function getDirectories() : int
    {
        $uniqueDirectoryNames = \array_unique($this->directoryNames);
        return \count($uniqueDirectoryNames) - 1;
    }
    public function getFiles() : int
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
    /**
     * @api used in tests
     */
    public function getLogicalLines() : int
    {
        return $this->logicalLineCount;
    }
    public function getClassLinesRelative() : float
    {
        if ($this->logicalLineCount > 0) {
            return $this->relative($this->getClassLines(), $this->logicalLineCount);
        }
        return 0.0;
    }
    public function getClassLines() : int
    {
        return \array_sum($this->classLineCountPerClass);
    }
    public function getAverageClassLength() : float
    {
        if ($this->classLineCountPerClass === []) {
            return 0.0;
        }
        return $this->average($this->getClassLines(), \count($this->classLineCountPerClass));
    }
    public function getMaxClassLength() : int
    {
        return \max($this->classLineCountPerClass);
    }
    public function getAverageMethodLength() : float
    {
        if ($this->methodLineCountPerMethod === []) {
            return 0.0;
        }
        $totalMethodLineCount = \array_sum($this->methodLineCountPerMethod);
        return $this->average($totalMethodLineCount, \count($this->methodLineCountPerMethod));
    }
    public function getMaxMethodLength() : int
    {
        return \max($this->methodLineCountPerMethod);
    }
    public function getFunctionLines() : int
    {
        return $this->functionLineCount;
    }
    public function getNotInClassesOrFunctions() : int
    {
        return $this->logicalLineCount - $this->getClassLines() - $this->functionLineCount;
    }
    public function getNamespaces() : int
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
    public function getConstantCount() : int
    {
        return $this->globalConstantCount + $this->getClassConstants();
    }
    public function getGlobalConstantCount() : int
    {
        return $this->globalConstantCount;
    }
    public function getPublicClassConstants() : int
    {
        return $this->publicClassConstantCount;
    }
    public function getNonPublicClassConstants() : int
    {
        return $this->nonPublicClassConstantCount;
    }
    public function getClassConstants() : int
    {
        return $this->publicClassConstantCount + $this->nonPublicClassConstantCount;
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
    public function getFunctionLinesRelative() : float
    {
        if ($this->logicalLineCount > 0) {
            return $this->relative($this->functionLineCount, $this->logicalLineCount);
        }
        return 0.0;
    }
    public function getNotInClassesOrFunctionsRelative() : float
    {
        if ($this->logicalLineCount > 0) {
            return $this->relative($this->getNotInClassesOrFunctions(), $this->logicalLineCount);
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
    public function getGlobalConstantCountRelative() : float
    {
        if ($this->getConstantCount() !== 0) {
            return $this->relative($this->globalConstantCount, $this->getConstantCount());
        }
        return 0.0;
    }
    public function getClassConstantCountRelative() : float
    {
        if ($this->getConstantCount() !== 0) {
            return $this->relative($this->getClassConstants(), $this->getConstantCount());
        }
        return 0.0;
    }
    public function getPublicClassConstantsRelative() : float
    {
        if ($this->getClassConstants() !== 0) {
            return $this->relative($this->publicClassConstantCount, $this->getClassConstants());
        }
        return 0.0;
    }
    public function getNonPublicClassConstantsRelative() : float
    {
        if ($this->getClassConstants() !== 0) {
            return $this->relative($this->nonPublicClassConstantCount, $this->getClassConstants());
        }
        return 0.0;
    }
    public function getEnumCount() : int
    {
        return $this->enumCount;
    }
    private function relative(int $partialNumber, int $totalNumber) : float
    {
        $relative = $partialNumber / $totalNumber * 100;
        return NumberFormat::singleDecimal($relative);
    }
    private function average(int $partialNumber, int $totalNumber) : float
    {
        $relative = $partialNumber / $totalNumber;
        return NumberFormat::singleDecimal($relative);
    }
}
