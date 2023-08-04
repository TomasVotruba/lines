<?php

declare(strict_types=1);

namespace TomasVotruba\Lines\NodeVisitor;

use PhpParser\Node;
use PhpParser\Node\Expr\Closure;
use PhpParser\Node\Expr\FuncCall;
use PhpParser\Node\Name;
use PhpParser\Node\Stmt\Class_;
use PhpParser\Node\Stmt\ClassLike;
use PhpParser\Node\Stmt\ClassMethod;
use PhpParser\Node\Stmt\Enum_;
use PhpParser\Node\Stmt\Function_;
use PhpParser\Node\Stmt\Interface_;
use PhpParser\Node\Stmt\Namespace_;
use PhpParser\Node\Stmt\Trait_;
use PhpParser\NodeVisitorAbstract;
use TomasVotruba\Lines\Measurements;

final class StructureNodeVisitor extends NodeVisitorAbstract
{
    public function __construct(
        private readonly Measurements $measurements
    ) {
    }

    public function enterNode(Node $node): ?Node
    {
        if ($node instanceof ClassLike) {
            $this->measureClassLikes($node);
            return $node;
        }

        if ($node instanceof ClassMethod) {
            $this->measureClassMethod($node);
            return $node;
        }

        if ($this->isDefineFuncCall($node)) {
            $this->measurements->incrementGlobalConstantCount();
            return $node;
        }

        if ($node instanceof Namespace_) {
            if (! $node->name instanceof Name) {
                return null;
            }

            $namespaceName = $node->name->toString();
            $this->measurements->addNamespace($namespaceName);

            return $node;
        }

        if ($node instanceof Function_ || $node instanceof Closure) {
            $this->measurements->incrementFunctionCount();

            return $node;
        }

        return null;
    }

    private function measureClassLikes(ClassLike $classLike): void
    {
        if ($classLike instanceof Class_) {
            $constantCount = count($classLike->getConstants());
            $this->measurements->incrementClassConstants($constantCount);

            if ($classLike->isAnonymous()) {
                return;
            }

            $this->measurements->incrementClassCount();
        }

        if ($classLike instanceof Enum_) {
            $this->measurements->incrementEnumCount();
            return;
        }

        if ($classLike instanceof Interface_) {
            $this->measurements->incrementInterfaceCount();
            return;
        }

        if ($classLike instanceof Trait_) {
            $this->measurements->incrementTraitCount();
        }
    }

    private function measureClassMethod(ClassMethod $classMethod): void
    {
        if ($classMethod->isPrivate()) {
            $this->measurements->incrementPrivateMethods();
        } elseif ($classMethod->isProtected()) {
            $this->measurements->incrementProtectedMethods();
        } elseif ($classMethod->isPublic()) {
            $this->measurements->incrementPublicMethods();
        }

        if ($classMethod->isStatic()) {
            $this->measurements->incrementStaticMethods();
        } else {
            $this->measurements->incrementNonStaticMethods();
        }
    }

    private function isDefineFuncCall(Node $node): bool
    {
        if (! $node instanceof FuncCall) {
            return false;
        }

        if (! $node->name instanceof Name) {
            return false;
        }

        return $node->name->toLowerString() === 'define';
    }
}
