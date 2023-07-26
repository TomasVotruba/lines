<?php

declare(strict_types=1);

namespace TomasVotruba\Lines;

use Webmozart\Assert\Assert;

/**
 * @see \TomasVotruba\Lines\Tests\AnalyserTest
 */
final class Analyser
{
    private readonly MetricsCollector $metricsCollector;

    public function __construct()
    {
        $this->metricsCollector = new MetricsCollector();
    }

    /**
     * @param string[] $files
     *
     * @return array<string, mixed>
     */
    public function countFiles(array $files): array
    {
        Assert::allString($files);
        Assert::allFileExists($files);

        foreach ($files as $file) {
            $this->countFile($file);
        }

        return $this->metricsCollector->getPublisher()->toArray();
    }

    private function countFile(string $filename): void
    {
        Assert::fileExists($filename);

        $buffer = file_get_contents($filename);
        Assert::string($buffer);

        $this->metricsCollector->incrementLines(substr_count($buffer, "\n"));
        $tokens = token_get_all($buffer);
        $numTokens = count($tokens);

        unset($buffer);

        $this->metricsCollector->addFile($filename);

        $blocks = [];
        $currentBlock = false;
        $namespace = false;
        $className = null;
        $functionName = null;
        $this->metricsCollector->currentClassReset();
        $isLogicalLine = true;
        $isInMethod = false;

        for ($i = 0; $i < $numTokens; ++$i) {
            if (is_string($tokens[$i])) {
                $token = trim($tokens[$i]);

                if ($token === ';') {
                    if ($isLogicalLine) {
                        if ($className !== null) {
                            $this->metricsCollector->currentClassIncrementLines();

                            if ($functionName !== null) {
                                $this->metricsCollector->currentMethodIncrementLines();
                            }
                        } elseif ($functionName !== null) {
                            $this->metricsCollector->incrementFunctionLines();
                        }

                        $this->metricsCollector->incrementLogicalLines();
                    }

                    $isLogicalLine = true;
                } elseif ($token === '{') {
                    if ($currentBlock === T_CLASS) {
                        $block = $className;
                    } elseif ($currentBlock === T_FUNCTION) {
                        $block = $functionName;
                    } else {
                        $block = false;
                    }

                    $blocks[] = $block;

                    $currentBlock = false;
                } elseif ($token === '}') {
                    $block = array_pop($blocks);

                    if ($block !== false && $block !== null) {
                        if ($block === $functionName) {
                            $functionName = null;

                            if ($isInMethod) {
                                $this->metricsCollector->currentMethodStop();
                                $isInMethod = false;
                            }
                        } elseif ($block === $className) {
                            $className = null;
                            $this->metricsCollector->currentClassStop();
                            $this->metricsCollector->currentClassReset();
                        }
                    }
                }

                continue;
            }

            [$token, $value] = $tokens[$i];

            switch ($token) {
                case T_NAMESPACE:
                    $namespace = $this->getNamespaceName($tokens, $i);

                    if (is_string($namespace)) {
                        $this->metricsCollector->addNamespace($namespace);
                    }

                    $isLogicalLine = false;

                    break;

                case T_CLASS:
                case T_INTERFACE:
                case T_TRAIT:
                    if (! $this->isClassDeclaration($tokens, $i)) {
                        break;
                    }

                    $this->metricsCollector->currentClassReset();
                    $className = $this->getClassName($namespace ?: '', $tokens, $i);
                    $currentBlock = T_CLASS;

                    if ($token === T_TRAIT) {
                        $this->metricsCollector->incrementTraits();
                    } elseif ($token === T_INTERFACE) {
                        $this->metricsCollector->incrementInterfaces();
                    } else {
                        $classModifierToken = $this->getPreviousNonWhitespaceNonCommentTokenPos($tokens, $i);

                        if ($classModifierToken &&
                            $tokens[$classModifierToken][0] === T_ABSTRACT
                        ) {
                            $this->metricsCollector->incrementAbstractClasses();
                        } elseif (
                            $classModifierToken &&
                            $tokens[$classModifierToken][0] === T_FINAL
                        ) {
                            $this->metricsCollector->incrementFinalClasses();
                        } else {
                            $this->metricsCollector->incrementNonFinalClasses();
                        }
                    }

                    break;

                case T_FUNCTION:
                    $prev = $this->getPreviousNonWhitespaceTokenPos($tokens, $i);

                    if ($tokens[$prev][0] === T_USE) {
                        break;
                    }

                    $currentBlock = T_FUNCTION;

                    $next = $this->getNextNonWhitespaceTokenPos($tokens, $i);

                    if (is_int($next) && ($tokens[$next] === '&' || (is_array($tokens[$next]) && $tokens[$next][1] === '&'))) {
                        $next = $this->getNextNonWhitespaceTokenPos($tokens, $next);
                    }

                    if (is_array($tokens[$next]) &&
                        $tokens[$next][0] === T_STRING) {
                        $functionName = $tokens[$next][1];
                    } else {
                        $currentBlock = 'anonymous function';
                        $functionName = 'anonymous function';
                        $this->metricsCollector->incrementAnonymousFunctions();
                    }

                    if ($currentBlock === T_FUNCTION) {
                        if ($className === null &&
                            $functionName !== 'anonymous function') {
                            $this->metricsCollector->incrementNamedFunctions();
                        } else {
                            $static = false;
                            $visibility = T_PUBLIC;

                            for ($j = $i; $j > 0; --$j) {
                                if (is_string($tokens[$j])) {
                                    if ($tokens[$j] === '{' ||
                                        $tokens[$j] === '}' ||
                                        $tokens[$j] === ';') {
                                        break;
                                    }

                                    continue;
                                }

                                switch ($tokens[$j][0]) {
                                    case T_PRIVATE:
                                        $visibility = T_PRIVATE;

                                        break;

                                    case T_PROTECTED:
                                        $visibility = T_PROTECTED;

                                        break;

                                    case T_STATIC:
                                        $static = true;

                                        break;
                                }
                            }

                            $isInMethod = true;
                            $this->metricsCollector->currentMethodStart();

                            $this->metricsCollector->currentClassIncrementMethods();

                            if (! $static) {
                                $this->metricsCollector->incrementNonStaticMethods();
                            } else {
                                $this->metricsCollector->incrementStaticMethods();
                            }

                            if ($visibility === T_PUBLIC) {
                                $this->metricsCollector->incrementPublicMethods();
                            } elseif ($visibility === T_PROTECTED) {
                                $this->metricsCollector->incrementProtectedMethods();
                            } elseif ($visibility === T_PRIVATE) {
                                $this->metricsCollector->incrementPrivateMethods();
                            }
                        }
                    }

                    break;

                case T_CURLY_OPEN:
                    $currentBlock = T_CURLY_OPEN;
                    $blocks[] = $currentBlock;

                    break;

                case T_DOLLAR_OPEN_CURLY_BRACES:
                    $currentBlock = T_DOLLAR_OPEN_CURLY_BRACES;
                    $blocks[] = $currentBlock;

                    break;

                case T_IF:
                case T_ELSEIF:
                case T_FOR:
                case T_FOREACH:
                case T_WHILE:
                case T_CASE:
                case T_CATCH:
                case T_BOOLEAN_AND:
                case T_LOGICAL_AND:
                case T_BOOLEAN_OR:
                case T_LOGICAL_OR:
                    break;

                case T_COMMENT:
                case T_DOC_COMMENT:
                    // We want to count all intermediate lines before the token ends
                    // But sometimes a new token starts after a newline, we don't want to count that.
                    // That happened with /* */ and /**  */, but not with // since it'll end at the end
                    $this->metricsCollector->incrementCommentLines(substr_count(rtrim($value, "\n"), "\n") + 1);

                    break;
                case T_CONST:
                    $possibleScopeToken = $this->getPreviousNonWhitespaceNonCommentTokenPos($tokens, $i);

                    if ($possibleScopeToken &&
                        in_array($tokens[$possibleScopeToken][0], [T_PRIVATE, T_PROTECTED], true)
                    ) {
                        $this->metricsCollector->incrementNonPublicClassConstants();
                    } else {
                        $this->metricsCollector->incrementPublicClassConstants();
                    }

                    break;

                case T_STRING:
                    if ($value === 'define') {
                        $this->metricsCollector->incrementGlobalConstants();

                        $j = $i + 1;

                        while (isset($tokens[$j]) && $tokens[$j] !== ';') {
                            if (is_array($tokens[$j]) &&
                                $tokens[$j][0] === T_CONSTANT_ENCAPSED_STRING) {
                                $this->metricsCollector->addConstant(str_replace("'", '', $tokens[$j][1]));

                                break;
                            }

                            ++$j;
                        }
                    }

                    break;

                case T_DOUBLE_COLON:
                case T_OBJECT_OPERATOR:
                    $n = $this->getNextNonWhitespaceTokenPos($tokens, $i);
                    Assert::integer($n);

                    $nn = $this->getNextNonWhitespaceTokenPos($tokens, $n);

                    if ($n && $nn &&
                        isset($tokens[$n][0]) &&
                        ($tokens[$n][0] === T_STRING ||
                         $tokens[$n][0] === T_VARIABLE) &&
                        $tokens[$nn] === '(') {
                        if ($token === T_DOUBLE_COLON) {
                            $this->metricsCollector->incrementStaticMethodCalls();
                        } else {
                            $this->metricsCollector->incrementNonStaticMethodCalls();
                        }
                    }

                    break;

                case T_USE:
                case T_DECLARE:
                    $isLogicalLine = false;

                    break;
            }
        }
    }

    /**
     * @param array<int, mixed> $tokens
     */
    private function getNamespaceName(array $tokens, int $i): ?string
    {
        if (isset($tokens[$i + 2][1])) {
            $namespace = $tokens[$i + 2][1];

            for ($j = $i + 3; ; $j += 2) {
                if (isset($tokens[$j]) && $tokens[$j][0] === T_NS_SEPARATOR) {
                    $namespace .= '\\' . $tokens[$j + 1][1];
                } else {
                    break;
                }
            }

            return $namespace;
        }

        return null;
    }

    /**
     * @param array<int, mixed> $tokens
     */
    private function getClassName(string $namespace, array $tokens, int $i): string
    {
        $i += 2;

        if (! isset($tokens[$i][1])) {
            return 'invalid class name';
        }

        $className = $tokens[$i][1];

        $namespaced = $className === '\\';

        while (isset($tokens[$i + 1]) && is_array($tokens[$i + 1]) && $tokens[$i + 1][0] !== T_WHITESPACE) {
            $className .= $tokens[++$i][1];
        }

        if (! $namespaced && $namespace !== false) {
            $className = $namespace . '\\' . $className;
        }

        return strtolower((string) $className);
    }

    /**
     * @param array<int, mixed> $tokens
     */
    private function getNextNonWhitespaceTokenPos(array $tokens, int $start): int|bool
    {
        if (isset($tokens[$start + 1])) {
            if (isset($tokens[$start + 1][0]) &&
                $tokens[$start + 1][0] === T_WHITESPACE &&
                isset($tokens[$start + 2])) {
                return $start + 2;
            }

            return $start + 1;
        }

        return false;
    }

    /**
     * @param array<int, mixed> $tokens
     */
    private function getPreviousNonWhitespaceTokenPos(array $tokens, int $start): int|bool
    {
        if (isset($tokens[$start - 1])) {
            if (isset($tokens[$start - 1][0]) &&
                $tokens[$start - 1][0] === T_WHITESPACE &&
                isset($tokens[$start - 2])) {
                return $start - 2;
            }

            return $start - 1;
        }

        return false;
    }

    /**
     * @param array<int, mixed> $tokens
     */
    private function getPreviousNonWhitespaceNonCommentTokenPos(array $tokens, int $start): int|bool
    {
        $previousTokenIndex = $start - 1;

        if (isset($tokens[$previousTokenIndex])) {
            if (in_array($tokens[$previousTokenIndex][0], [
                T_WHITESPACE,
                T_COMMENT,
                T_DOC_COMMENT,
            ], true)
            ) {
                return $this->getPreviousNonWhitespaceNonCommentTokenPos($tokens, $previousTokenIndex);
            }

            return $previousTokenIndex;
        }

        return false;
    }

    /**
     * @param array<int, mixed> $tokens
     */
    private function isClassDeclaration(array $tokens, int $i): bool
    {
        $n = $this->getPreviousNonWhitespaceTokenPos($tokens, $i);

        return ! isset($tokens[$n]) ||
            ! is_array($tokens[$n]) ||
            ! in_array($tokens[$n][0], [T_DOUBLE_COLON, T_NEW], true);
    }
}
