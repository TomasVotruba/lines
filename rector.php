<?php

declare(strict_types=1);

use Rector\Config\RectorConfig;

return RectorConfig::configure()
    ->withPaths([__DIR__ . '/bin', __DIR__ . '/src', __DIR__ . '/tests'])
    ->withPreparedSets(
        codeQuality: true,
        naming: true,
        codingStyle: true,
        privatization: true,
        deadCode: true,
        typeDeclarations: true,
        typeDeclarationDocblocks: true,
    )
    ->withPhpSets()
    ->withImportNames()
    ->withSkip([
        '*/Fixture/*',
    ]);
