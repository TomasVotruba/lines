<?php

declare (strict_types=1);
namespace Lines202509\PhpParser\Builder;

use Lines202509\PhpParser;
use Lines202509\PhpParser\BuilderHelpers;
abstract class Declaration implements PhpParser\Builder
{
    /** @var array<string, mixed> */
    protected $attributes = [];
    /**
     * Adds a statement.
     *
     * @param PhpParser\Node\Stmt|PhpParser\Builder $stmt The statement to add
     *
     * @return $this The builder instance (for fluid interface)
     */
    public abstract function addStmt($stmt);
    /**
     * Adds multiple statements.
     *
     * @param (PhpParser\Node\Stmt|PhpParser\Builder)[] $stmts The statements to add
     *
     * @return $this The builder instance (for fluid interface)
     */
    public function addStmts(array $stmts)
    {
        foreach ($stmts as $stmt) {
            $this->addStmt($stmt);
        }
        return $this;
    }
    /**
     * Sets doc comment for the declaration.
     *
     * @param PhpParser\Comment\Doc|string $docComment Doc comment to set
     *
     * @return $this The builder instance (for fluid interface)
     */
    public function setDocComment($docComment)
    {
        $this->attributes['comments'] = [BuilderHelpers::normalizeDocComment($docComment)];
        return $this;
    }
}
