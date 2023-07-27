<?php

declare(strict_types=1);

namespace TomasVotruba\Lines\Console\OutputFormatter;

use Symfony\Component\Console\Output\OutputInterface;
use TomasVotruba\Lines\MeasurementResult;
use Webmozart\Assert\Assert;

final class JsonOutputFormatter
{
    public function printResult(MeasurementResult $measurementResult, OutputInterface $output): void
    {
        $directories = [];

        if ($measurementResult['directories'] > 0) {
            $directories = [
                'directories' => $measurementResult['directories'],
                'files' => $measurementResult['files'],
            ];
        }

        unset($measurementResult['directories'], $measurementResult['files']);

        $completeReport = array_merge($directories, $measurementResult);

        $jsonString = json_encode($completeReport, JSON_PRETTY_PRINT);
        Assert::string($jsonString);

        $output->writeln($jsonString);
    }
}
