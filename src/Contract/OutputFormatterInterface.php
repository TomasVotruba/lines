<?php

declare (strict_types=1);
namespace Lines202308\TomasVotruba\Lines\Contract;

use Lines202308\Symfony\Component\Console\Output\OutputInterface;
use Lines202308\TomasVotruba\Lines\Measurements;
interface OutputFormatterInterface
{
    public function printMeasurement(Measurements $measurements, OutputInterface $output, bool $isShort) : void;
}
