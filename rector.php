<?php

declare(strict_types=1);

use Rector\Config\RectorConfig;

return RectorConfig::configure()
    ->withPaths([__DIR__ . '/bin', __DIR__ . '/src', __DIR__ . '/tests'])
    ->withPreparedSets(
        codeQuality: true,
        codingStyle: true,
        naming: true,
        privatization: true,
        deadCode: true,
        typeDeclarations: true,
        typeDeclarationDocblocks: true,
        earlyReturn: true,
        phpunitCodeQuality: true,
    )
    ->withPhpSets()
    ->withImportNames(removeUnusedImports: true)
    ->withSkip([
        '*/Fixture/*',
    ]);
