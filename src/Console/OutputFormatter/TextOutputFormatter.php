<?php

declare(strict_types=1);

namespace TomasVotruba\Lines\Console\OutputFormatter;

use Symfony\Component\Console\Helper\TableSeparator;
use Symfony\Component\Console\Helper\TableStyle;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use TomasVotruba\Lines\Helpers\NumberFormat;

final class TextOutputFormatter
{
    public function __construct(
        private readonly SymfonyStyle $symfonyStyle,
    ) {
    }

    /**
     * @param array<string, mixed> $count
     */
    public function printResult(array $count, OutputInterface $output): void
    {
        $padLeftTableStyle = new TableStyle();
        $padLeftTableStyle->setPadType(STR_PAD_LEFT);

        if ($count['directories'] > 0) {
            $this->symfonyStyle->createTable()
                ->setColumnWidth(0, 30)
                ->setColumnWidth(1, 19)
                ->setHeaders(['Metric', 'Count'])
                ->setRows([['Directories', $count['directories']], ['Files', $count['files']]])
                ->setColumnStyle(1, $padLeftTableStyle)
                ->render();

            $this->symfonyStyle->newLine();
        }

        $tableRows = [
            [
                'Comments',
                NumberFormat::pretty($count['cloc']),
                NumberFormat::percent($count['loc'] > 0 ? ($count['cloc'] / $count['loc']) * 100 : 0),
            ],

            [
                'Code',
                NumberFormat::pretty($count['ncloc']),
                NumberFormat::percent($count['loc'] > 0 ? ($count['ncloc'] / $count['loc']) * 100 : 0),
            ],

            [new TableSeparator(), new TableSeparator(), new TableSeparator()],

            [
                '<options=bold>Total</>',
                '<options=bold>' . NumberFormat::pretty($count['loc']) . '</>',
                '<options=bold>100.0 %</>',
            ],
        ];

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
            $count['llocClasses'],
            $count['lloc'] > 0 ? ($count['llocClasses'] / $count['lloc']) * 100 : 0,
            $count['classLlocAvg'],
            $count['classLlocMin'],
            $count['classLlocMax'],
            $count['methodLlocAvg'],
            $count['methodLlocMin'],
            $count['methodLlocMax'],
            $count['averageMethodsPerClass'],
            $count['minimumMethodsPerClass'],
            $count['maximumMethodsPerClass'],
            $count['llocFunctions'],
            $count['lloc'] > 0 ? ($count['llocFunctions'] / $count['lloc']) * 100 : 0,
            $count['llocByNof'],
            $count['llocGlobal'],
            $count['lloc'] > 0 ? ($count['llocGlobal'] / $count['lloc']) * 100 : 0,
            $count['namespaces'],
            $count['interfaces'],
            $count['traits'],
            $count['classes'],
            $count['methods'],
            $count['nonStaticMethods'],
            $count['methods'] > 0 ? ($count['nonStaticMethods'] / $count['methods']) * 100 : 0,
            $count['staticMethods'],
            $count['methods'] > 0 ? ($count['staticMethods'] / $count['methods']) * 100 : 0,
            $count['publicMethods'],
            $count['methods'] > 0 ? ($count['publicMethods'] / $count['methods']) * 100 : 0,
            $count['protectedMethods'],
            $count['methods'] > 0 ? ($count['protectedMethods'] / $count['methods']) * 100 : 0,
            $count['privateMethods'],
            $count['methods'] > 0 ? ($count['privateMethods'] / $count['methods']) * 100 : 0,
            $count['functions'],
            $count['namedFunctions'],
            $count['functions'] > 0 ? ($count['namedFunctions'] / $count['functions']) * 100 : 0,
            $count['anonymousFunctions'],
            $count['functions'] > 0 ? ($count['anonymousFunctions'] / $count['functions']) * 100 : 0,
            $count['constants'],
            $count['globalConstants'],
            $count['constants'] > 0 ? ($count['globalConstants'] / $count['constants']) * 100 : 0,
            $count['classConstants'],
            $count['constants'] > 0 ? ($count['classConstants'] / $count['constants']) * 100 : 0,
            $count['publicClassConstants'],
            $count['classConstants'] > 0 ? ($count['publicClassConstants'] / $count['classConstants']) * 100 : 0,
            $count['nonPublicClassConstants'],
            $count['classConstants'] > 0 ? ($count['nonPublicClassConstants'] / $count['classConstants']) * 100 : 0
        );

        $output->writeln($result);
    }
}
