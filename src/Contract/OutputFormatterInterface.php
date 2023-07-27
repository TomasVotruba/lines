<?php

declare(strict_types=1);

namespace TomasVotruba\Lines\Contract;

use Symfony\Component\Console\Output\OutputInterface;
use TomasVotruba\Lines\MeasurementResult;

interface OutputFormatterInterface
{
    public function printResult(MeasurementResult $measurementResult, OutputInterface $output): void;
}
