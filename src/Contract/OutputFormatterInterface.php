<?php

declare(strict_types=1);

namespace TomasVotruba\Lines\Contract;

use Symfony\Component\Console\Output\OutputInterface;
use TomasVotruba\Lines\Measurements;

interface OutputFormatterInterface
{
    public function printResult(Measurements $measurements, OutputInterface $output): void;
}
