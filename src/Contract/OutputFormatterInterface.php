<?php

declare(strict_types=1);

namespace TomasVotruba\Lines\Contract;

use TomasVotruba\Lines\Measurements;

interface OutputFormatterInterface
{
    public function printMeasurement(Measurements $measurements, bool $isShort): void;
}
