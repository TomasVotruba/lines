<?php

declare(strict_types=1);

use Rector\CodeQuality\Rector\Class_\InlineConstructorDefaultToPropertyRector;
use Rector\Config\RectorConfig;
use Rector\PHPUnit\CodeQuality\Rector\Class_\YieldDataProviderRector;
use Rector\Set\ValueObject\LevelSetList;

return static function (RectorConfig $rectorConfig): void {
    $rectorConfig->importNames();

    $rectorConfig->rules([
        YieldDataProviderRector::class,
    ]);

    $rectorConfig->paths([
        __DIR__ . '/src',
        __DIR__ . '/tests',
    ]);

    $rectorConfig->sets([
        LevelSetList::UP_TO_PHP_81,
        \Rector\PHPUnit\Set\PHPUnitSetList::PHPUNIT_100,
    ]);
};
