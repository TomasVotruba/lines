<?php

declare(strict_types=1);

use Symplify\EasyCodingStandard\Config\ECSConfig;
use Symplify\EasyCodingStandard\ValueObject\Set\SetList;

return function (ECSConfig $ecsConfig): void {
    $ecsConfig->paths([
        __DIR__ . '/src',
        __DIR__ . '/tests',
    ]);

    $ecsConfig->skip([
        '*/Fixture/*'
    ]);

    $ecsConfig->sets([
         SetList::COMMON,
         SetList::PSR_12,
         SetList::SYMPLIFY,
    ]);
};
