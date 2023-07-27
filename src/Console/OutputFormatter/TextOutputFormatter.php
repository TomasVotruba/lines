<?php

declare(strict_types=1);

namespace TomasVotruba\Lines\Console\OutputFormatter;

use Symfony\Component\Console\Helper\TableSeparator;
use Symfony\Component\Console\Helper\TableStyle;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use TomasVotruba\Lines\Helpers\NumberFormat;
use TomasVotruba\Lines\MeasurementResult;

final class TextOutputFormatter
{
    public function __construct(
        private readonly SymfonyStyle $symfonyStyle,
    ) {
    }

    public function printResult(MeasurementResult $measurementResult, OutputInterface $output): void
    {
        $this->printFilesAndDirectories($measurementResult);
        $this->printLinesOfCode($measurementResult);

        $format = <<<'END'
Size
    Classes
        Lines                       %10d (%.2f%%)
        Length
            Average                 %10d
            Minimum                 %10d
            Maximum                 %10d
    Methods
        Length
            Average                 %10d
            Min                     %10d
            Max                     %10d
        Methods Per Class
                Average                     %10d
                Minimum                     %10d
                Maximum                     %10d
    Functions                               %10d (%.2f%%)
        Average Length                      %10d
    Not in classes or functions             %10d (%.2f%%)

Structure
    Namespaces                              %10d
    Interfaces                              %10d
    Traits                                  %10d
    Classes                                 %10d
        Methods                             %10d
            Scope
                Non-Static                  %10d (%.2f%%)
                Static                      %10d (%.2f%%)
            Visibility
                Public                      %10d (%.2f%%)
                Protected                   %10d (%.2f%%)
                Private                     %10d (%.2f%%)
            Functions                       %10d
                Named                       %10d (%.2f%%)
                Anonymous                   %10d (%.2f%%)
            Constants                       %10d
                Global                      %10d (%.2f%%)
                Class                       %10d (%.2f%%)
                Public                      %10d (%.2f%%)
                Non-Public                  %10d (%.2f%%)
END;

        $result = sprintf(
            $format,
            $measurementResult->getClassLines(),
            $measurementResult->getLogicalLines() > 0 ? ($measurementResult->getClassLines() / $measurementResult->getLogicalLines()) * 100 : 0,
            $measurementResult['classLlocAvg'],
            $measurementResult['classLlocMin'],
            $measurementResult['classLlocMax'],
            $measurementResult['methodLlocAvg'],
            $measurementResult['methodLlocMin'],
            $measurementResult['methodLlocMax'],
            $measurementResult['averageMethodsPerClass'],
            $measurementResult['minimumMethodsPerClass'],
            $measurementResult['maximumMethodsPerClass'],
            $measurementResult['llocFunctions'],
            $measurementResult['lloc'] > 0 ? ($measurementResult['llocFunctions'] / $measurementResult['lloc']) * 100 : 0,
            $measurementResult['llocByNof'],
            $measurementResult['llocGlobal'],
            $measurementResult['lloc'] > 0 ? ($measurementResult['llocGlobal'] / $measurementResult['lloc']) * 100 : 0,
            $measurementResult['namespaces'],
            $measurementResult['interfaces'],
            $measurementResult['traits'],
            $measurementResult['classes'],
            $measurementResult['methods'],
            $measurementResult['nonStaticMethods'],
            $measurementResult['methods'] > 0 ? ($measurementResult['nonStaticMethods'] / $measurementResult['methods']) * 100 : 0,
            $measurementResult['staticMethods'],
            $measurementResult['methods'] > 0 ? ($measurementResult['staticMethods'] / $measurementResult['methods']) * 100 : 0,
            $measurementResult['publicMethods'],
            $measurementResult['methods'] > 0 ? ($measurementResult['publicMethods'] / $measurementResult['methods']) * 100 : 0,
            $measurementResult['protectedMethods'],
            $measurementResult['methods'] > 0 ? ($measurementResult['protectedMethods'] / $measurementResult['methods']) * 100 : 0,
            $measurementResult['privateMethods'],
            $measurementResult['methods'] > 0 ? ($measurementResult['privateMethods'] / $measurementResult['methods']) * 100 : 0,
            $measurementResult['functions'],
            $measurementResult['namedFunctions'],
            $measurementResult['functions'] > 0 ? ($measurementResult['namedFunctions'] / $measurementResult['functions']) * 100 : 0,
            $measurementResult['anonymousFunctions'],
            $measurementResult['functions'] > 0 ? ($measurementResult['anonymousFunctions'] / $measurementResult['functions']) * 100 : 0,
            $measurementResult['constants'],
            $measurementResult['globalConstants'],
            $measurementResult['constants'] > 0 ? ($measurementResult['globalConstants'] / $measurementResult['constants']) * 100 : 0,
            $measurementResult['classConstants'],
            $measurementResult['constants'] > 0 ? ($measurementResult['classConstants'] / $measurementResult['constants']) * 100 : 0,
            $measurementResult['publicClassConstants'],
            $measurementResult['classConstants'] > 0 ? ($measurementResult['publicClassConstants'] / $measurementResult['classConstants']) * 100 : 0,
            $measurementResult['nonPublicClassConstants'],
            $measurementResult['classConstants'] > 0 ? ($measurementResult['nonPublicClassConstants'] / $measurementResult['classConstants']) * 100 : 0
        );

        $output->writeln($result);
    }

    private function printFilesAndDirectories(MeasurementResult $measurementResult): void
    {
        $padLeftTableStyle = new TableStyle();
        $padLeftTableStyle->setPadType(STR_PAD_LEFT);

        $this->symfonyStyle->createTable()
            ->setColumnWidth(0, 30)
            ->setColumnWidth(1, 19)
            ->setHeaders(['Metric', 'Count'])
            ->setRows(
                [['Directories', $measurementResult->getDirectories()], ['Files', $measurementResult->getFiles()]]
            )
            ->setColumnStyle(1, $padLeftTableStyle)
            ->render();

        $this->symfonyStyle->newLine();
    }

    private function printLinesOfCode(MeasurementResult $measurementResult): void
    {
        $tableRows = [
            [
                'Comments',
                NumberFormat::pretty($measurementResult['cloc']),
                NumberFormat::percent(
                    $measurementResult['loc'] > 0 ? ($measurementResult['cloc'] / $measurementResult['loc']) * 100 : 0
                ),
            ],

            [
                'Code',
                NumberFormat::pretty($measurementResult['ncloc']),
                NumberFormat::percent(
                    $measurementResult['loc'] > 0 ? ($measurementResult['ncloc'] / $measurementResult['loc']) * 100 : 0
                ),
            ],

            [new TableSeparator(), new TableSeparator(), new TableSeparator()],

            [
                '<options=bold>Total</>',
                '<options=bold>' . NumberFormat::pretty($measurementResult['loc']) . '</>',
                '<options=bold>100.0 %</>',
            ],
        ];

        $padLeftTableStyle = new TableStyle();
        $padLeftTableStyle->setPadType(STR_PAD_LEFT);

        $this->symfonyStyle->createTable()
            ->setColumnWidth(0, 30)
            ->setColumnWidth(1, 8)
            ->setColumnWidth(2, 7)
            ->setHeaders(['Lines of code', 'Count', 'Relative'])
            ->setRows($tableRows)
            ->setColumnStyle(1, $padLeftTableStyle)
            ->setColumnStyle(2, $padLeftTableStyle)
            ->render();

        $this->symfonyStyle->newLine(2);
    }
}
