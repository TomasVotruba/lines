<?php

declare (strict_types=1);
namespace Lines202308\TomasVotruba\Lines\Contract;

use Lines202308\TomasVotruba\Lines\Measurements;
interface OutputFormatterInterface
{
    public function printMeasurement(Measurements $measurements, bool $isShort) : void;
}
