<?php

declare (strict_types=1);
namespace Lines202401\TomasVotruba\Lines\Contract;

use Lines202401\TomasVotruba\Lines\Measurements;
interface OutputFormatterInterface
{
    public function printMeasurement(Measurements $measurements, bool $isShort) : void;
}
