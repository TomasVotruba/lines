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
        $arrayData = [
            'directories' => $measurementResult->getDirectories(),
            'files' => $measurementResult->getFiles(),
        ];

        // @todo
        //$completeReport = array_merge($directories, $measurementResult);

        $jsonString = json_encode($arrayData, JSON_PRETTY_PRINT);
        Assert::string($jsonString);

        $output->writeln($jsonString);
    }
}
