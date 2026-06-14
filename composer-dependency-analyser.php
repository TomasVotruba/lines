<?php

declare(strict_types=1);

use ShipMonk\ComposerDependencyAnalyser\Config\Configuration;
use ShipMonk\ComposerDependencyAnalyser\Config\ErrorType;

return (new Configuration())
    // intentional fixture class that cannot be autoloaded
    ->ignoreErrorsOnPath(__DIR__ . '/tests/Fixture', [ErrorType::UNKNOWN_CLASS]);
