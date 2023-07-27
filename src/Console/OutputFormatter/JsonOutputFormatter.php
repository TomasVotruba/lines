<?php

declare(strict_types=1);

namespace TomasVotruba\Lines\Console\OutputFormatter;

use Symfony\Component\Console\Output\OutputInterface;
use TomasVotruba\Lines\Contract\OutputFormatterInterface;
use TomasVotruba\Lines\Measurements;
use Webmozart\Assert\Assert;

final class JsonOutputFormatter implements OutputFormatterInterface
{
    public function printResult(Measurements $measurements, OutputInterface $output): void
    {
        $arrayData = [
            'directories' => $measurements->getDirectories(),
            'files' => $measurements->getFiles(),
        ];

        // @todo
        //$completeReport = array_merge($directories, $measurements);

        $jsonString = json_encode($arrayData, JSON_PRETTY_PRINT);
        Assert::string($jsonString);

        $output->writeln($jsonString);
    }
}
