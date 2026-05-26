<?php

declare (strict_types=1);
namespace Lines202605\TomasVotruba\Lines\Contract;

use Lines202605\TomasVotruba\Lines\Measurements;
interface OutputFormatterInterface
{
    public function printMeasurement(Measurements $measurements, bool $isShort, bool $showLongestFiles) : void;
}
