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

            // Replace array dim fetch with method calls
            $measurementResult->getAverageClassLength(),
            $measurementResult->getMinimumClassLength(),
            $measurementResult->getMaximumClassLength(),
            $measurementResult->getAverageMethodLength(),
            $measurementResult->getMinimumMethodLength(),
            $measurementResult->getMaximumMethodLength(),
            $measurementResult->getAverageMethodsPerClass(),
            $measurementResult->getMinimumMethodsPerClass(),
            $measurementResult->getMaximumMethodsPerClass(),
            $llocFunctions = $measurementResult->getFunctionLines(),
            $measurementResult->getLogicalLines() > 0 ? ($llocFunctions / $measurementResult->getLogicalLines()) * 100 : 0,
            $measurementResult->getAverageFunctionLength(),
            $llocGlobal = $measurementResult->getNotInClassesOrFunctions(),
            $measurementResult->getLogicalLines() > 0 ? ($llocGlobal / $measurementResult->getLogicalLines()) * 100 : 0,
            $measurementResult->getNamespaces(),
            $measurementResult->getInterfaces(),
            $measurementResult->getTraits(),
            $measurementResult->getClasses(),
            $methods = $measurementResult->getMethods(),
            $nonStaticMethods = $measurementResult->getNonStaticMethods(),
            $methods > 0 ? ($nonStaticMethods / $methods) * 100 : 0,
            $staticMethods = $measurementResult->getStaticMethods(),
            $methods > 0 ? ($staticMethods / $methods) * 100 : 0,
            $publicMethods = $measurementResult->getPublicMethods(),
            $methods > 0 ? ($publicMethods / $methods) * 100 : 0,
            $protectedMethods = $measurementResult->getProtectedMethods(),
            $methods > 0 ? ($protectedMethods / $methods) * 100 : 0,
            $privateMethods = $measurementResult->getPrivateMethods(),
            $methods > 0 ? ($privateMethods / $methods) * 100 : 0,
            $functions = $measurementResult->getFunctions(),
            $namedFunctions = $measurementResult->getNamedFunctions(),
            $functions > 0 ? ($namedFunctions / $functions) * 100 : 0,
            $anonymousFunctions = $measurementResult->getAnonymousFunctions(),
            $functions > 0 ? ($anonymousFunctions / $functions) * 100 : 0,
            $constants = $measurementResult->getConstants(),
            $globalConstants = $measurementResult->getGlobalConstants(),
            $constants > 0 ? ($globalConstants / $constants) * 100 : 0,
            $classConstants = $measurementResult->getClassConstants(),
            $constants > 0 ? ($classConstants / $constants) * 100 : 0,
            $publicClassConstants = $measurementResult->getPublicClassConstants(),
            $classConstants > 0 ? ($publicClassConstants / $classConstants) * 100 : 0,
            $nonPublicClassConstants = $measurementResult->getNonPublicClassConstants(),
            $classConstants > 0 ? ($nonPublicClassConstants / $classConstants) * 100 : 0
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
                NumberFormat::pretty($measurementResult->getCommentLines()),
                NumberFormat::percent(
                    $measurementResult->getLines() > 0 ? ($measurementResult->getCommentLines() / $measurementResult->getLines()) * 100 : 0
                ),
            ],

            [
                'Code',
                NumberFormat::pretty($measurementResult->getNonCommentLines()),
                NumberFormat::percent(
                    $measurementResult->getLines() > 0 ? ($measurementResult->getNonCommentLines() / $measurementResult->getLines()) * 100 : 0
                ),
            ],

            [new TableSeparator(), new TableSeparator(), new TableSeparator()],

            [
                '<options=bold>Total</>',
                '<options=bold>' . NumberFormat::pretty($measurementResult->getLines()) . '</>',
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
