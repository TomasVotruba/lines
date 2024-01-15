<?php

declare(strict_types=1);

use Symplify\EasyCodingStandard\Config\ECSConfig;

return ECSConfig::configure()
    ->withPaths([
        __DIR__ . '/src',
        __DIR__ . '/tests',
    ])
    ->withSkip([
        '*/Fixture/*'
    ])
    ->withPreparedSets(psr12: true, common: true, strict: true, symplify: true);
