<?php

declare (strict_types=1);
namespace Lines202308\TomasVotruba\Lines\NodeVisitor;

use Lines202308\PhpParser\Node;
use Lines202308\PhpParser\Node\Expr\Closure;
use Lines202308\PhpParser\Node\Expr\FuncCall;
use Lines202308\PhpParser\Node\Name;
use Lines202308\PhpParser\Node\Stmt\Class_;
use Lines202308\PhpParser\Node\Stmt\ClassLike;
use Lines202308\PhpParser\Node\Stmt\ClassMethod;
use Lines202308\PhpParser\Node\Stmt\Enum_;
use Lines202308\PhpParser\Node\Stmt\Function_;
use Lines202308\PhpParser\Node\Stmt\Interface_;
use Lines202308\PhpParser\Node\Stmt\Namespace_;
use Lines202308\PhpParser\Node\Stmt\Trait_;
use Lines202308\PhpParser\NodeVisitorAbstract;
use Lines202308\TomasVotruba\Lines\Measurements;
final class StructureNodeVisitor extends NodeVisitorAbstract
{
    /**
     * @readonly
     * @var \TomasVotruba\Lines\Measurements
     */
    private $measurements;
    public function __construct(Measurements $measurements)
    {
        $this->measurements = $measurements;
    }
    public function enterNode(Node $node) : ?Node
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
            if (!$node->name instanceof Name) {
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
    private function measureClassLikes(ClassLike $classLike) : void
    {
        if ($classLike instanceof Class_) {
            $constantCount = \count($classLike->getConstants());
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
    private function measureClassMethod(ClassMethod $classMethod) : void
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
    private function isDefineFuncCall(Node $node) : bool
    {
        if (!$node instanceof FuncCall) {
            return \false;
        }
        if (!$node->name instanceof Name) {
            return \false;
        }
        return $node->name->toLowerString() === 'define';
    }
}
