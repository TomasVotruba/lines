<?php

declare (strict_types=1);
namespace Lines202402\TomasVotruba\Lines\Contract;

use Lines202402\TomasVotruba\Lines\Measurements;
interface OutputFormatterInterface
{
    public function printMeasurement(Measurements $measurements, bool $isShort) : void;
}
