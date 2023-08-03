<?php

declare (strict_types=1);
namespace Lines202308\TomasVotruba\Lines;

use Lines202308\Webmozart\Assert\Assert;
/**
 * @see \TomasVotruba\Lines\Tests\AnalyserTest
 */
final class Analyser
{
    public function __construct()
    {
        // define fallback constants for PHP 8.0 tokens in case of e.g. PHP 7.2 run
        if (!\defined('T_MATCH')) {
            \define('T_MATCH', 5000);
        }
        if (!\defined('T_READONLY')) {
            \define('T_READONLY', 5010);
        }
        if (!\defined('T_ENUM')) {
            \define('T_ENUM', 5015);
        }
    }
    /**
     * @param string[] $filePaths
     */
    public function measureFiles(array $filePaths, ?callable $progressBarClosure = null) : Measurements
    {
        $measurements = new Measurements();
        Assert::allString($filePaths);
        Assert::allFileExists($filePaths);
        foreach ($filePaths as $filePath) {
            $this->measureFile($measurements, $filePath);
            if (\is_callable($progressBarClosure)) {
                $progressBarClosure();
            }
        }
        return $measurements;
    }
    private function measureFile(Measurements $measurements, string $filePath) : void
    {
        Assert::fileExists($filePath);
        $fileContents = \file_get_contents($filePath);
        Assert::string($fileContents);
        $newlinesCount = \substr_count($fileContents, "\n");
        $measurements->incrementLines($newlinesCount);
        $tokens = \token_get_all($fileContents);
        $numTokens = \count($tokens);
        // performance?
        unset($fileContents);
        $measurements->addFile($filePath);
        $blocks = [];
        $currentBlock = \false;
        $namespace = \false;
        $className = null;
        $functionName = null;
        $measurements->resetCurrentClass();
        $isLogicalLine = \true;
        $isInMethod = \false;
        for ($i = 0; $i < $numTokens; ++$i) {
            if (\is_string($tokens[$i])) {
                $token = \trim($tokens[$i]);
                if ($token === ';') {
                    if ($isLogicalLine) {
                        if ($className !== null) {
                            $measurements->incrementCurrentClassLines();
                            if ($functionName !== null) {
                                $measurements->currentMethodIncrementLines();
                            }
                        } elseif ($functionName !== null) {
                            $measurements->incrementFunctionLines();
                        }
                    }
                    $isLogicalLine = \true;
                } elseif ($token === '{') {
                    if ($currentBlock === \T_CLASS) {
                        $block = $className;
                    } elseif ($currentBlock === \T_FUNCTION) {
                        $block = $functionName;
                    } else {
                        $block = \false;
                    }
                    $blocks[] = $block;
                    $currentBlock = \false;
                } elseif ($token === '}') {
                    $block = \array_pop($blocks);
                    if ($block !== \false && $block !== null) {
                        if ($block === $functionName) {
                            $functionName = null;
                            if ($isInMethod) {
                                $measurements->currentMethodStop();
                                $isInMethod = \false;
                            }
                        } elseif ($block === $className) {
                            $className = null;
                            $measurements->resetCurrentClass();
                        }
                    }
                }
                continue;
            }
            [$token, $value] = $tokens[$i];
            switch ($token) {
                case \T_NAMESPACE:
                    $namespace = $this->getNamespaceName($tokens, $i);
                    if (\is_string($namespace)) {
                        $measurements->addNamespace($namespace);
                    }
                    $isLogicalLine = \false;
                    break;
                // php 8.0+
                case \T_ENUM:
                    $measurements->incrementEnums();
                    break;
                case \T_CLASS:
                case \T_INTERFACE:
                case \T_TRAIT:
                    if (!$this->isClassDeclaration($tokens, $i)) {
                        break;
                    }
                    $measurements->resetCurrentClass();
                    $className = $this->getClassName($namespace ?: '', $tokens, $i);
                    $currentBlock = \T_CLASS;
                    if ($token === \T_TRAIT) {
                        $measurements->incrementTraits();
                    } elseif ($token === \T_INTERFACE) {
                        $measurements->incrementInterfaces();
                    } else {
                        $measurements->incrementClasses();
                    }
                    break;
                case \T_FUNCTION:
                    $prev = $this->getPreviousNonWhitespaceTokenPos($tokens, $i);
                    if ($tokens[$prev][0] === \T_USE) {
                        break;
                    }
                    $currentBlock = \T_FUNCTION;
                    $next = $this->getNextNonWhitespaceTokenPos($tokens, $i);
                    if (\is_int($next) && ($tokens[$next] === '&' || \is_array($tokens[$next]) && $tokens[$next][1] === '&')) {
                        $next = $this->getNextNonWhitespaceTokenPos($tokens, $next);
                    }
                    if (\is_array($tokens[$next]) && $tokens[$next][0] === \T_STRING) {
                        $functionName = $tokens[$next][1];
                    } else {
                        $currentBlock = 'anonymous function';
                        $functionName = 'anonymous function';
                        $measurements->incrementFunctions();
                    }
                    if ($currentBlock === \T_FUNCTION) {
                        if ($className === null && $functionName !== 'anonymous function') {
                            $measurements->incrementFunctions();
                        } else {
                            $static = \false;
                            $visibility = \T_PUBLIC;
                            for ($j = $i; $j > 0; --$j) {
                                if (\is_string($tokens[$j])) {
                                    if ($tokens[$j] === '{' || $tokens[$j] === '}' || $tokens[$j] === ';') {
                                        break;
                                    }
                                    continue;
                                }
                                switch ($tokens[$j][0]) {
                                    case \T_PRIVATE:
                                        $visibility = \T_PRIVATE;
                                        break;
                                    case \T_PROTECTED:
                                        $visibility = \T_PROTECTED;
                                        break;
                                    case \T_STATIC:
                                        $static = \true;
                                        break;
                                }
                            }
                            $isInMethod = \true;
                            $measurements->currentMethodStart();
                            $measurements->currentClassIncrementMethods();
                            if (!$static) {
                                $measurements->incrementNonStaticMethods();
                            } else {
                                $measurements->incrementStaticMethods();
                            }
                            if ($visibility === \T_PUBLIC) {
                                $measurements->incrementPublicMethods();
                            } elseif ($visibility === \T_PROTECTED) {
                                $measurements->incrementProtectedMethods();
                            } elseif ($visibility === \T_PRIVATE) {
                                $measurements->incrementPrivateMethods();
                            }
                        }
                    }
                    break;
                case \T_CURLY_OPEN:
                    $currentBlock = \T_CURLY_OPEN;
                    $blocks[] = $currentBlock;
                    break;
                case \T_DOLLAR_OPEN_CURLY_BRACES:
                    $currentBlock = \T_DOLLAR_OPEN_CURLY_BRACES;
                    $blocks[] = $currentBlock;
                    break;
                case \T_IF:
                case \T_ELSEIF:
                case \T_FOR:
                case \T_FOREACH:
                case \T_WHILE:
                case \T_CASE:
                case \T_CATCH:
                case \T_BOOLEAN_AND:
                case \T_LOGICAL_AND:
                case \T_BOOLEAN_OR:
                case \T_LOGICAL_OR:
                    break;
                case \T_COMMENT:
                case \T_DOC_COMMENT:
                    // We want to count all intermediate lines before the token ends
                    // But sometimes a new token starts after a newline, we don't want to count that.
                    // That happened with /* */ and /**  */, but not with // since it'll end at the end
                    $measurements->incrementCommentLines(\substr_count(\rtrim($value, "\n"), "\n") + 1);
                    break;
                case \T_CONST:
                    $measurements->incrementClassConstants();
                    break;
                case \T_STRING:
                    if ($value === 'define') {
                        $measurements->incrementGlobalConstants();
                    }
                    break;
                case \T_DOUBLE_COLON:
                case \T_OBJECT_OPERATOR:
                    break;
                case \T_USE:
                case \T_DECLARE:
                    $isLogicalLine = \false;
                    break;
            }
        }
    }
    /**
     * @param array<int, mixed> $tokens
     */
    private function getNamespaceName(array $tokens, int $i) : ?string
    {
        if (isset($tokens[$i + 2][1])) {
            $namespace = $tokens[$i + 2][1];
            for ($j = $i + 3; isset($tokens[$j]) && $tokens[$j][0] === \T_NS_SEPARATOR; $j += 2) {
                $namespace .= '\\' . $tokens[$j + 1][1];
            }
            return $namespace;
        }
        return null;
    }
    /**
     * @param array<int, mixed> $tokens
     */
    private function getClassName(string $namespace, array $tokens, int $i) : string
    {
        $i += 2;
        if (!isset($tokens[$i][1])) {
            return 'invalid class name';
        }
        $className = $tokens[$i][1];
        $namespaced = $className === '\\';
        while (isset($tokens[$i + 1]) && \is_array($tokens[$i + 1]) && $tokens[$i + 1][0] !== \T_WHITESPACE) {
            $className .= $tokens[++$i][1];
        }
        if (!$namespaced && $namespace !== \false) {
            $className = $namespace . '\\' . $className;
        }
        return \strtolower((string) $className);
    }
    /**
     * @param array<int, mixed> $tokens
     * @return int|bool
     */
    private function getNextNonWhitespaceTokenPos(array $tokens, int $start)
    {
        if (isset($tokens[$start + 1])) {
            if (isset($tokens[$start + 1][0]) && $tokens[$start + 1][0] === \T_WHITESPACE && isset($tokens[$start + 2])) {
                return $start + 2;
            }
            return $start + 1;
        }
        return \false;
    }
    /**
     * @param array<int, mixed> $tokens
     * @return int|bool
     */
    private function getPreviousNonWhitespaceTokenPos(array $tokens, int $start)
    {
        if (isset($tokens[$start - 1])) {
            if (isset($tokens[$start - 1][0]) && $tokens[$start - 1][0] === \T_WHITESPACE && isset($tokens[$start - 2])) {
                return $start - 2;
            }
            return $start - 1;
        }
        return \false;
    }
    /**
     * @param array<int, mixed> $tokens
     */
    private function isClassDeclaration(array $tokens, int $i) : bool
    {
        $n = $this->getPreviousNonWhitespaceTokenPos($tokens, $i);
        return !isset($tokens[$n]) || !\is_array($tokens[$n]) || !\in_array($tokens[$n][0], [\T_DOUBLE_COLON, \T_NEW], \true);
    }
}
