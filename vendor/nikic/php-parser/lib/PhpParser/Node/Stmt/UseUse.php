<?php

declare (strict_types=1);
namespace Lines202606\PhpParser\Node\Stmt;

use Lines202606\PhpParser\Node\UseItem;
require __DIR__ . '/../UseItem.php';
if (\false) {
    /**
     * For classmap-authoritative support.
     *
     * @deprecated use \PhpParser\Node\UseItem instead.
     */
    class UseUse extends UseItem
    {
    }
}
