<?php

declare (strict_types=1);
namespace Lines202307\TomasVotruba\Lines\Contract;

use Lines202307\Symfony\Component\Console\Output\OutputInterface;
use Lines202307\TomasVotruba\Lines\Measurements;
interface OutputFormatterInterface
{
    public function printMeasurement(Measurements $measurements, OutputInterface $output) : void;
}
