<?php

declare (strict_types=1);
namespace Lines202508\PhpParser\Node\Stmt;

use Lines202508\PhpParser\Node\UseItem;
require __DIR__ . '/../UseItem.php';
if (\false) {
    // For classmap-authoritative support.
    class UseUse extends UseItem
    {
    }
}
