<?php

declare (strict_types=1);
namespace Lines202412\TomasVotruba\Lines\Contract;

use Lines202412\TomasVotruba\Lines\Measurements;
interface OutputFormatterInterface
{
    public function printMeasurement(Measurements $measurements, bool $isShort, bool $showLongestFiles) : void;
}
