<?php

declare(strict_types=1);

namespace TomasVotruba\Lines\NodeVisitor;

use PhpParser\Node;
use PhpParser\Node\Stmt\Class_;
use PhpParser\Node\Stmt\ClassLike;
use PhpParser\Node\Stmt\ClassMethod;
use PhpParser\Node\Stmt\Enum_;
use PhpParser\Node\Stmt\Interface_;
use PhpParser\Node\Stmt\Trait_;
use PhpParser\NodeVisitorAbstract;
use TomasVotruba\Lines\Measurements;

final class StructureNodeVisitor extends NodeVisitorAbstract
{
    public function __construct(
        private readonly Measurements $measurements
    ) {
    }

    public function enterNode(Node $node): ?\PhpParser\Node
    {
        if ($node instanceof ClassLike) {
            $this->measureClassLikes($node);
            return $node;
        }

        if ($node instanceof ClassMethod) {
            $this->measureClassMethod($node);
            return $node;
        }

        if ($node instanceof Node\Stmt\Namespace_) {
            if (! $node->name instanceof \PhpParser\Node\Name) {
                return null;
            }

            $namespaceName = $node->name->toString();
            $this->measurements->addNamespace($namespaceName);
        }

        return null;
    }

    private function measureClassLikes(ClassLike $classLike): void
    {
        if ($classLike instanceof Class_) {
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

    private function measureClassMethod(ClassMethod $node): void
    {
        if ($node->isPrivate()) {
            $this->measurements->incrementPrivateMethods();
        } elseif ($node->isProtected()) {
            $this->measurements->incrementProtectedMethods();
        } elseif ($node->isPublic()) {
            $this->measurements->incrementPublicMethods();
        }

        if ($node->isStatic()) {
            $this->measurements->incrementStaticMethods();
        } else {
            $this->measurements->incrementNonStaticMethods();
        }
    }
}
